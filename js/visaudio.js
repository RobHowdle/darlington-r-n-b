//variables
let audio, dir, ext, playList, playListindex, playListurl, playBtn, pauseBtn, stopBtn, prevBtn, nextBtn, 
curTime, durTime, position, fillbar, trackBar, volume, volUp, volMute, trackData, track, songIinfo, artist, ttitle, album,
vrotate, imgDir, img, imgIndex, imgExt,  meta, fileExt, fileReader, fileData;

//visualiser
let mplayer, canvas, ctxt, centeX, centeY, radius, bars, xEnd, yEnd, vis,barHeight, barWidth, filechosen, ppimage, frequency_array;

//buttons
prevBtn = document.getElementById("prevbtn");
playBtn = document.getElementById("playbtn");
pauseBtn = document.getElementById("pausebtn");
stopBtn = document.getElementById("stopbtn");
nextBtn = document.getElementById("nextbtn");

//time indicators
curTime = document.getElementById("audio-currenttime");
durTime = document.getElementById("audio-duration");
fillbar = document.getElementById("fillbar");
trackBar = document.getElementById("trackbar");

//volume
volume = document.getElementById("apvolume");
volUp = document.getElementById("vol-up");
volMute = document.getElementById("vol-mute");

//playlist & track info
playList = document.getElementById("playlist");
ttitle = document.getElementById("title");
vrotate = document.getElementById("visualiser");

//audio initialisation
function initAudio(){
    audio = new Audio();
    dir = "audio/";
    ext = ".mp3";
    playListindex = 0;
    audio.src = dir+playList[playListindex].value+ext;
    console.log(audio.src);
    // audio.play();

    //visualiser initialisation
    context = new (window.AudioContext || window.webkitAudioContext)();
    // context = new (window.AudioContext)();
    analyser = context.createAnalyser();
    source = context.createMediaElementSource(audio);
    source.connect(analyser);
    analyser.connect(context.destination);
    frequency_array = new Uint8Array(analyser.frequencyBinCount);
    animationLooper();

    document.documentElement.addEventListener(
    "mousedown", function(){
      mouse_IsDown = true;
      if (context.state !== 'running') {
      context.resume();
    }})

    title.classList.add('hidden');
    vrotate.classList.remove('vrotate');
    // vrotate.classList.add('vrotate');


    // audio.addEventListener("ended",function(){
    //     playListindex = 0;
    //     playList.value = playList[playListindex].value;
    //     console.log('play List index '+playListindex);
    //     console.log('play List value '+playList.value);
    // });

    //button functions
    playBtn.addEventListener("click", function(){
        
        audio.play();
        console.log('play btn '+audio.src);
        playBtn.style.display = "none";
        pauseBtn.style.display = "inline-block";
        title.classList.remove('hidden');
        vrotate.classList.add('vrotate');
        //set track title
        title.innerHTML = playList[playListindex].value;
        playList.value = playList[playListindex].value;
        
        
    })

    pauseBtn.addEventListener("click", function(){
        audio.pause();
        playBtn.style.display = "inline-block";
        pauseBtn.style.display = "none";
        title.classList.remove('hidden');
        vrotate.classList.remove('vrotate');
    })

    //There is NO stop so we use the pause and reset the track time to 0
    // setting current time to 0 means that if play is clicked again the track starts from the begining 
    //not where stopped as in paused
    stopBtn.addEventListener("click",function(){
        audio.pause();
        audio.currentTime = 0;
        playListindex = 0;
        playList.value = playList[playListindex].value;
        audio.src = dir+playList[playListindex].value+ext;
        // console.log('play List index '+playListindex);
        // console.log('play List value '+playList.value);
        // console.log('audio Source '+audio.src);
        playBtn.style.display = "inline-block";
        pauseBtn.style.display = "none";
        title.classList.add('hidden');
        vrotate.classList.remove('vrotate');
    })

    nextBtn.addEventListener("click",function(){
        if(playListindex === playList.length-1){
            playListindex = 0;
        }else{
            playListindex++;
        }

        audio.src = dir+playList[playListindex].value+ext;
        audio.play();
        playBtn.style.display = "none";
        pauseBtn.style.display = "inline-block";
        title.classList.remove('hidden');
        vrotate.classList.add('vrotate');
  
        //set track title
        title.innerHTML = playList[playListindex].value;
        playList.value = playList[playListindex].value;
        
 
    })

    prevBtn.addEventListener("click",function(){
        if(playListindex === 0){
            playListindex = playList.length-1;
        }else{
            playListindex--;
        }

        audio.src = dir+playList[playListindex].value+ext;
        audio.play();
        playBtn.style.display = "none";
        pauseBtn.style.display = "inline-block";
        title.classList.remove('hidden');
        vrotate.classList.add('vrotate');

        //set track title
        title.innerHTML = playList[playListindex].value;
        playList.value = playList[playListindex].value;

    })

    // Time Update
    audio.addEventListener("timeupdate",function(){

    // Audio Current time update
    if(audio.readyState > 0){
        let minutes = parseInt((audio.currentTime /60) % 60); // % 60 is modulus rounded up to the nearest full minute
        let seconds = parseInt(audio.currentTime % 60);
        
            if(seconds < 10){
                seconds = "0"+ seconds //adds leading 0 if less than 10 seconds
            }
        
            if(minutes < 10){
                minutes= "0"+ minutes //adds leading 0 if less than 10 minutes
            }
        
            curTime.innerHTML = minutes+":"+seconds; //replaces the html 00:00 within the current time span tags
        }
    
        // Full Audio Duration
        if(audio.readyState > 0){
            let minutes = parseInt((audio.duration /60) % 60); // % 60 is modulus rounded up to the nearest full minute
            let seconds = parseInt(audio.duration % 60);
    
            if(seconds < 10){
                seconds = "0"+ seconds //adds leading 0 if less than 10 seconds
            }
    
            if(minutes < 10){
                minutes= "0"+ minutes //adds leading 0 if less than 10 minutes
           }
    
            durTime.innerHTML = minutes+":"+seconds; //replaces the html 00:00 within the duration time span tags
        }
    
        // Fillbar on timeupdate
        position = audio.currentTime / audio.duration; //position of slider 
        fillbar.style.width = position * 100 + "%";
   
    })

    //update fillbar and current time "on click"
    trackBar.addEventListener("click", function (e){ //e or event
        let percent = e.offsetX / this.offsetWidth //"this" is the trackbar
        audio.currentTime = percent * audio.duration;
        fillbar.value = perecent / 100;
    })

    // volume settings
    volume.addEventListener("mousemove", function (){
        audio.volume = this.value / 100;
    })

    // mute - unmute
    volUp.addEventListener("click", function(){
        audio.muted = true;
        volUp.style.display = "none";
        volMute.style.display = "inline-block";
    })

    volMute.addEventListener("click", function(){
        audio.muted = false;
        volUp.style.display = "inline-block";
        volMute.style.display = "none";
    })

   //play next track in playlist
   audio.addEventListener("ended",function(){
    if(playListindex === playList.length-1){
        playListindex = 0;
    }else{
        playListindex++;
    }

    audio.src = dir+playList[playListindex].value+ext;
    audio.play();
    playBtn.style.display = "none";
    pauseBtn.style.display = "inline-block";
    title.classList.remove('hidden');
    vrotate.classList.add('vrotate');

    //set track title
    title.innerHTML = playList[playListindex].value;
    playList.value = playList[playListindex].value;
    // playList.value.style.backgroundColor = 'red';

    })

    //play track on click event
    playList.addEventListener("click", function(){
        audio.src = dir+ this.value+ext;
        console.log(audio.src);
        // playList[playListindex] = this.value
        // console.log(playList[playListindex]);
        audio.play();
        playBtn.style.display = "none";
        pauseBtn.style.display = "inline-block";
        title.classList.remove('hidden');
        vrotate.classList.add('vrotate');

        //set track title, note use of "this.value" instead of playList[playListindex].value
        title.innerHTML = this.value;
        playList.value = this.value;
        // playList.value.style.backgroundColor = 'red';

    })

}

function animationLooper(){
    // set to the size of device
    vis = document.getElementById("visualiser");
    vis.width = window.innerWidth;
    // canvas.height = window.innerHeight; set height to width below to get "square" canvas
    vis.height = window.innerWidth;
    ctxt = vis.getContext("2d");
    // find the center of the window x/2 and y/2 
    centeX = vis.width / 2;
    centeY = vis.height / 2;
    //set cirlce radius
    radius = (0.16 * vis.width);
    bars = 300;
    barWidth = 2;

    // style the background with gradient, comment out for transparent background over css background image
    // var gradient = ctxt.createLinearGradient(0,0,0,canvas.height);
    // gradient.addColorStop(0,"rgba(35, 7, 77, 1)");
    // gradient.addColorStop(1,"rgba(204, 83, 51, 1)");
    // ctxt.fillStyle = gradient;
    // ctxt.fillRect(0,0,canvas.width,canvas.height);

    //draw a circle
    ctxt.beginPath();
    ctxt.arc(centeX,centeY,radius,0,2*Math.PI);
    // circle stroke commented out to give no circle line, ie "imaginary" circle
    // ctxt.stroke();
   
    analyser.getByteFrequencyData(frequency_array);
    for(var i = 0; i < bars; i++){
        //divide a circle into equal parts
        rads = Math.PI * 2 / bars;
        barHeight = frequency_array[i]*(radius*0.008);
        // set coordinates and use math bits to draw lines around the "imgainary" circle
        x = centeX + Math.cos(rads * i) * (radius);
        y = centeY + Math.sin(rads * i) * (radius);
        xEnd = centeX + Math.cos(rads * i)*(radius + barHeight);
        yEnd = centeY + Math.sin(rads * i)*(radius + barHeight);
        //draw a bar
        drawBar(x, y, xEnd, yEnd, barWidth,frequency_array[i]);
    }
    window.requestAnimationFrame(animationLooper);

 
}
//drawing a bars
function drawBar(x1, y1, x2, y2, width,frequency){
    //set line colours
    var lineColor = "rgb(" + 55 + ", " + frequency + ", " + 255 + ")";
    ctxt.strokeStyle = lineColor;
    ctxt.lineWidth = width;
    ctxt.beginPath();
    ctxt.moveTo(x1,y1);
    ctxt.lineTo(x2,y2);
    ctxt.stroke();
}

window.addEventListener("load", initAudio);

//resume audio on user action ie click page
// window.addEventListener("click", initAudio);


