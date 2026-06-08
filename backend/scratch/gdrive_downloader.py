import gdown
import re
import os

def extract_id(url):
    """
    Extracts the file ID from a Google Drive URL.
    """
    # Regex to find the ID in various URL formats
    patterns = [
        r'/file/d/([a-zA-Z0-9_-]+)',
        r'id=([a-zA-Z0-9_-]+)',
        r'([a-zA-Z0-9_-]+)$'
    ]
    
    for pattern in patterns:
        match = re.search(pattern, url)
        if match:
            return match.group(1)
    return None

def download_video(url, output_folder="downloads"):
    """
    Downloads a video from Google Drive using gdown.
    """
    file_id = extract_id(url)
    if not file_id:
        print(f"[-] Invalid URL: {url}")
        return

    if not os.path.exists(output_folder):
        os.makedirs(output_folder)

    # Construct the download URL
    download_url = f'https://drive.google.com/uc?id={file_id}'
    
    # Define output path (gdown will try to get the filename from headers if not specified, 
    # but we can provide a default if it fails)
    output_path = os.path.join(output_folder, f"video_{file_id}.mp4")

    print(f"[*] Attempting to download file ID: {file_id}")
    
    try:
        # gdown.download automatically handles the 'large file' virus warning
        filename = gdown.download(download_url, output_path, quiet=False)
        print(f"[+] Download complete: {filename}")
    except Exception as e:
        print(f"[-] Error downloading {url}: {e}")

if __name__ == "__main__":
    print("--- Google Drive Video Downloader ---")
    print("Paste your Google Drive link below (or type 'exit' to quit):")
    
    while True:
        user_input = input("\nURL: ").strip()
        if user_input.lower() == 'exit':
            break
        
        if user_input:
            download_video(user_input)
        else:
            print("Please enter a valid URL.")
