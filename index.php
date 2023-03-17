<?php
	if(isset($_POST['submit'])){
	$dir = "videos/";
	move_uploaded_file($_FILES["image"]["tmp_name"], $dir. $_FILES["image"]["name"]);
	echo "
	<script>
		window.location.href = 'http://127.0.0.1:23352/predict?imgPath=".$dir. $_FILES["image"]["name"]."'
	</script>
	";
	}
 ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8"> 
    <title>LIP READING APPLICATION</title>
    <meta name="viewport" content="width=device-width">
<style>
#rcorners1 {
  border-radius: 15px;
  background: #04AA6D;
  padding: 10px; 
  width: 1000px;
  height: 50px;  
}
#rcorners1:hover {background-color: #DA4C2D}

    .buttonload {
  background-color: #04AA6D; /* Green background */
  border: none; /* Remove borders */
  color: white; /* White text */
  padding: 12px 24px; /* Some padding */
  font-size: 16px; /* Set a font-size */
}

/* Add a right margin to each icon */
.fa {
  margin-left: -12px;
  margin-right: 8px;
}
.button {
  padding: 15px 25px;
  font-size: 24px;
  text-align: center;
  cursor: pointer;
  outline: none;
  color: #000000;
  background-color: #04AA6D;
  border: none;
  text-shadow: 2px 2px 5px white;
  border-radius: 15px;
  box-shadow: 0 9px #999;
}
body {
  background-color: #FFFF;
}
h1 {
  text-shadow: 2px 2px 5px white;
}
.button:hover {background-color: #DA4C2D}

.button:active {
  background-color: #3e8e41;
  box-shadow: 0 5px #666;
  transform: translateY(4px);
}


</style>
</head>
<script>
        
        let constraintObj = { 
            audio: false, 
            video: { 
                facingMode: "user", 
                width: { min: 640, ideal: 1280, max: 1920 },
                height: { min: 480, ideal: 720, max: 1080 } 
            } 
        }; 
     
        if (navigator.mediaDevices === undefined) {
            navigator.mediaDevices = {};
            navigator.mediaDevices.getUserMedia = function(constraintObj) {
                let getUserMedia = navigator.webkitGetUserMedia || navigator.mozGetUserMedia;
                if (!getUserMedia) {
                    return Promise.reject(new Error('getUserMedia is not implemented in this browser'));
                }
                return new Promise(function(resolve, reject) {
                    getUserMedia.call(navigator, constraintObj, resolve, reject);
                });
            }
        }else{
            navigator.mediaDevices.enumerateDevices()
            .then(devices => {
                devices.forEach(device=>{
                    console.log(device.kind.toUpperCase(), device.label);
                    //, device.deviceId
                })
            })
            .catch(err=>{
                console.log(err.name, err.message);
            })
        }

        navigator.mediaDevices.getUserMedia(constraintObj)
        .then(function(mediaStreamObj) {
            //connect the media stream to the first video element
            let video = document.querySelector('video');
            if ("srcObject" in video) {
                video.srcObject = mediaStreamObj;
            } else {
                //old version
                video.src = window.URL.createObjectURL(mediaStreamObj);
            }
            
            video.onloadedmetadata = function(ev) {
                //show in the video element what is being captured by the webcam
                video.play();
            };
            
            //add listeners for saving video/audio
            let start = document.getElementById('btnStart');
            let stop = document.getElementById('btnStop');
            let vidSave = document.getElementById('vid2');
            let mediaRecorder = new MediaRecorder(mediaStreamObj);
            let chunks = [];
            
            start.addEventListener('click', (ev)=>{
                mediaRecorder.start();
                console.log(mediaRecorder.state);
            }) 
            stop.addEventListener('click', (ev)=>{
                mediaRecorder.stop();
                console.log(mediaRecorder.state);
            });
            mediaRecorder.ondataavailable = function(ev) {
                chunks.push(ev.data);
            }
            mediaRecorder.onstop = (ev)=>{
                let blob = new Blob(chunks, { 'type' : 'video/mp4;' });
                chunks = [];
                let videoURL = window.URL.createObjectURL(blob);
                vidSave.src = videoURL;
               // mediaStreamObj.getTracks()[0].stop();
            }
        })
        .catch(function(err) { 
            console.log(err.name, err.message); 
        });
      
</script>

<body>

         <center>      <h1 id="rcorners1"> <center> <b> LIP READING APPLICATION </b> </center> </h1>  </center>
       
         
<div>
<main>
    <center>
       	<button id="btnStart" class="button" > <b> START RECORDING </b></button> <whitespace>
          
         <button id="btnStop"  class="button"><b>STOP RECORDING </b> </button>
        <br> 
         </br>
        	<video controls></video>
             <video id="vid2" controls> </video>
      </div>
</center>
<div>
    <center>
<form  action="" method="post" enctype="multipart/form-data">

      <input class="button" type="file"  name="image" id="file">
      <input class="button" type="submit" name="submit" id="submit" value="SUBMIT">
</form>


</div>       
 </main>


       </center>
</body>
</html>