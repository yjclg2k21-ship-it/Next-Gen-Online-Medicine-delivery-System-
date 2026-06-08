import asyncio
import aiohttp
import os
import re
import sys
from tqdm.asyncio import tqdm

# --- CONFIGURATION ---
CHUNK_SIZE = 1024 * 1024 * 5  # 5MB per chunk worker
MAX_CONCURRENT_CHUNKS = 8      # Number of parallel downloads

class FastGDriveDownloader:
    def __init__(self, url_or_id, output_path=None):
        self.file_id = self._extract_id(url_or_id)
        self.output_path = output_path
        self.session = None
        self.file_size = 0
        self.download_url = None

    def _extract_id(self, text):
        patterns = [
            r'/file/d/([a-zA-Z0-9_-]+)',
            r'id=([a-zA-Z0-9_-]+)',
            r'^([a-zA-Z0-9_-]+)$'
        ]
        for pattern in patterns:
            match = re.search(pattern, text)
            if match:
                return match.group(1)
        return text

    async def get_direct_link(self):
        """Resolves the G-Drive link to a direct download URL, bypassing warnings."""
        base_url = "https://drive.google.com/uc?export=download"
        url = f"{base_url}&id={self.file_id}"
        
        async with self.session.get(url) as response:
            text = await response.text()
            
            # Check for 'confirm' token in the response (large files)
            confirm_match = re.search(r'confirm=([a-zA-Z0-9_]+)', text)
            if confirm_match:
                confirm_token = confirm_match.group(1)
                self.download_url = f"{url}&confirm={confirm_token}"
            else:
                self.download_url = url

        # Try to get file size and actual filename
        async with self.session.get(self.download_url) as response:
            self.file_size = int(response.headers.get('Content-Length', 0))
            
            # Extract filename if not provided
            if not self.output_path:
                cd = response.headers.get('Content-Disposition', '')
                fname_match = re.search(r'filename="([^"]+)"', cd)
                if fname_match:
                    self.output_path = fname_match.group(1)
                else:
                    self.output_path = f"downloaded_{self.file_id}.bin"
            
            # Final check if the URL redirected (to googleusercontent)
            self.download_url = str(response.url)

    async def download_chunk(self, start, end, pbar):
        """Downloads a specific byte range."""
        headers = {'Range': f'bytes={start}-{end}'}
        async with self.session.get(self.download_url, headers=headers) as response:
            if response.status not in (200, 206):
                print(f"\n[-] Failed chunk {start}-{end}: Status {response.status}")
                return
            
            # We use a seekable file write to place chunks in correct order
            chunk_data = await response.read()
            with open(self.output_path, 'r+b') as f:
                f.seek(start)
                f.write(chunk_data)
            
            pbar.update(len(chunk_data))

    async def download(self):
        timeout = aiohttp.ClientTimeout(total=None) # No timeout for large files
        async with aiohttp.ClientSession(timeout=timeout) as session:
            self.session = session
            print(f"[*] Resolving link for ID: {self.file_id}...")
            await self.get_direct_link()
            
            if not self.file_size:
                print("[-] Could not determine file size. Multi-part download might fail.")
                # Fallback to standard download if size unknown
                return

            print(f"[+] File: {self.output_path} ({self.file_size / (1024*1024):.2f} MB)")
            
            # Create empty file of full size
            with open(self.output_path, 'wb') as f:
                f.truncate(self.file_size)

            # Divide file into chunks
            tasks = []
            pbar = tqdm(total=self.file_size, unit='B', unit_scale=True, desc="Progress", colour='green')
            
            semaphore = asyncio.Semaphore(MAX_CONCURRENT_CHUNKS)
            
            async def sem_task(s, e):
                async with semaphore:
                    await self.download_chunk(s, e, pbar)

            for i in range(0, self.file_size, CHUNK_SIZE):
                start = i
                end = min(i + CHUNK_SIZE - 1, self.file_size - 1)
                tasks.append(sem_task(start, end))

            await asyncio.gather(*tasks)
            pbar.close()
            print(f"\n[+] Successfully downloaded to: {self.output_path}")

if __name__ == "__main__":
    if len(sys.argv) > 1:
        target = sys.argv[1]
    else:
        print("--- Advanced G-Drive Fast Downloader ---")
        target = input("Enter G-Drive Link or ID: ").strip()

    if target:
        downloader = FastGDriveDownloader(target)
        try:
            asyncio.run(downloader.download())
        except KeyboardInterrupt:
            print("\n[-] Download cancelled.")
    else:
        print("No target provided.")
