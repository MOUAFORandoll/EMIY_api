import socket
import time

# Create a socket
s = socket.socket(socket.AF_INET, socket.SOCK_STREAM)

# Bind the socket to a port
s.bind(("localhost", 5000))

# Listen for connections
s.listen(5)

# Accept a connection
conn, addr = s.accept()

# Get the size of the file
file_size = conn.recv(1024)

# Open the file
f = open("../public/videos/shorts/\produitZt0RX.mp4", "rb")

# Read the file and send it to the client
while True:
    # Read 1024 bytes from the file
    data = f.read(1024)

    # If there is no more data, break
    if not data:
        break

    # Send the data to the client
    conn.send(data)

# Close the file
f.close()

# Close the connection
conn.close()