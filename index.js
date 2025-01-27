const express = require("express");
const app = express();
const http = require("http");
const server = http.createServer(app);
const { Server } = require("socket.io");
const io = new Server(server);
const fs = require("fs");

app.get("/", (req, res) => {
  res.sendFile(__dirname + "/index.html");
});

io.on("connection", (socket) => {
  console.log("client connected");

  socket.on("imgPath", (data) => {
    console.log("Image Path : ", data);

    socket.broadcast.emit("sendtoAI", data);
  });

  socket.on("keypointsData", (data) => {
    try {
      if (!data || typeof data !== "object") {
        throw new Error("Invalid keypoint data format");
      }
      console.log("Keypoint Update: ", data);
      socket.broadcast.emit("keypointUpdate", data);
    } catch (error) {
      console.error("Error handling keypointUpdate: ", error.message);
    }
  });

  socket.on("annotatedImage", (data) => {
    console.log("Annotated Image: ", data);
    const image = data;

    const buffer = Buffer.from(image, "base64");
    fs.writeFileSync("received_annotated_image.png", buffer);

    socket.broadcast.emit("imageToUnity", data);
  });
});

server.listen(3000, () => {
  console.log("listening on *:3000");
});
