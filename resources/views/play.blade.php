<x-guest-layout>
    <div class="pt-4 bg-gray-100">
        <div class="min-h-screen flex flex-col items-center pt-6 sm:pt-0">
            <div>
                <x-jet-authentication-card-logo />
            </div>

            <div class="w-full sm:max-w-2xl mt-6 p-6 bg-white shadow-md overflow-hidden sm:rounded-lg prose">
               
                
                <audio style="display: none" id="audio-playlist" controls  
                    poster="http://www.schillmania.com/projects/soundmanager2/demo/_image/soundmanager2-speaker.png" class="video-js vjs-default-skin">

          <source src='https://ybzx.yilindeli.com/albums/55/20171212102008.mp3' type="audio/mpeg">
                </audio>

                <div class="jp-type-playlist" style=''>
                <div id="audio-playlist-vjs-playlist" class='vjs-playlist jp-playlist' style='width:100%'>
                <ul>
                    <li >
                    <a class='vjs-track currentTrack' href='#track-0' data-index='0' data-src='https://ybzx.yilindeli.com/albums/55/20171212102008'> Born to Die </a><!--//note in the data-src's above that there are no file extensions, e.g., .m4a-->
                    </li>
                    <li >
                    <a class='vjs-track jp-playlist-item' href='#track-1' data-index='1' data-src='https://ybzx.yilindeli.com/albums/55/20171212102032'><!--//note in the data-src's above that there are no file extensions, e.g., .m4a-->
                    Off to the Races</a>
                    </li> 
                    <li >
                    <a class='vjs-track jp-playlist-item' href='#track-2' data-index='2' data-src='https://ybzx.yilindeli.com/albums/55/20171212102048'><!--//note in the data-src's above that there are no file extensions, e.g., .m4a-->
                    Blue Jeans</a>
                    </li>         
                </ul>
                </div>
                </div>
                
            </div>
        </div>
    </div>

    <link href="https://vjs.zencdn.net/7.11.4/video-js.css" rel="stylesheet" />

    <script src="https://vjs.zencdn.net/7.11.4/video.min.js"></script>
    {{-- <script src='http://tim-peterson.github.io/videojs-playlist/javascripts/videojs.playlist.js'></script> --}}
    <script>
        (function() {

videojs.plugin('playlist', function(options) {
 //this.L="vjs_common_one";
 

 console.log(this);
 var id=this.el().id;

 //console.log('begin playlist plugin with video id:'+id);

//console.log(this);
 //var id=this.tag.id;
 //assign variables
 var tracks=document.querySelectorAll("#"+id+"-vjs-playlist .vjs-track"),
     trackCount=tracks.length,
     player=this,
     currentTrack=tracks[0],
     index=0,
     play=true,
     onTrackSelected=options.onTrackSelected;

   //manually selecting track
   for(var i=0; i<trackCount; i++){
      tracks[i].onclick = function(){
         //var track=this;
         //index=this.getAttribute('data-index');
         //console.log("a is clicked and index position is"+this.getAttribute('data-index')+"the data-src is "+this.getAttribute('data-src'));
         //console.log("a is clicked and index position is"+index+"the data-src is "+this.getAttribute('data-src'));

         trackSelect(this);
      }
   }

   // for continuous play
   if(typeof options.continuous=='undefined' || options.continuous==true){
       //console.log('options.continuous==true');

       player.on("ended", function(){
           //console.log('on ended');

           index++;
           if(index>=trackCount){
             //console.log('go to beginning');
             index=0;
           }
           else;// console.log('trigger click next track');
           tracks[index].click();

       });// on ended
   }
   else;// console.log('dont play next!');

   //track select function for onended and manual selecting tracks
   var trackSelect=function(track){

      //get new src
       var src=track.getAttribute('data-src');
       index=parseInt(track.getAttribute('data-index')) || index;
       //console.log('track select click src:'+src);

       if(player.techName=='youtube'){
          player.src([
           { type: type="video/youtube", src:  src}
         ]);
       }
       else{

           if(player.el().firstChild.tagName=="AUDIO" || (typeof options.mediaType!='undefined' && options.mediaType=="audio") ){

             player.src([
                 { type: "audio/mp4", src:  src+".mp3" }
              ]);
           }
           else{
           //console.log("video");
             player.src([                
               { type: "video/mp4", src:  src+".mp4" },
               { type: "video/webm", src: src+".webm" }
               //{ type: "video/ogv", src: src+".ogv" }
             ]);
           }
       }



       if(play) player.play();

       //remove 'currentTrack' CSS class
       for(var i=0; i<trackCount; i++){
           if(tracks[i].classList.contains('currentTrack')){
               tracks[i].className=tracks[i].className.replace(/\bcurrentTrack\b/,'nonPlayingTrack');
           }
       }
       //add 'currentTrack' CSS class
       track.className = track.className + " currentTrack";
       if(typeof onTrackSelected === 'function') onTrackSelected.apply(track);

   }

   //if want to start at track other than 1st track
   if(typeof options.setTrack!='undefined' ){
     options.setTrack=parseInt(options.setTrack);
     currentTrack=tracks[options.setTrack];
     index=options.setTrack;
     play=false;
     //console.log('options.setTrack index'+index);
     trackSelect(tracks[index]);
     play=true;
   }

   var data={
     tracks: tracks,
     trackCount: trackCount,
     play:function(){
       return play;
     },
     index:function(){
       return index;
     },
     prev:function(){
       var j=index-1;
       //console.log('j'+j);
       if(j<0 || j>trackCount) j=0;
       trackSelect(tracks[j]);
     },
     next:function(){
       var j=index+1;
       //console.log('j'+j);
       if(j<0 || j>trackCount) j=0;
       trackSelect(tracks[j]);
     }
   };
   return data;
});
//return videojsplugin;
})();

    </script>
    <script>
        videojs("#audio-playlist", {"height":"auto", "width":"auto","customControlsOnMobile": true}).ready(function(event){
            var myPlayer=this;

            var playlist=myPlayer.playlist({
            'mediaType': 'audio',
            'continuous': true,
            'setTrack': 2
            });
            myPlayer.on('playing', function(){
                // var poster=document.getElementsByClassName("vjs-poster")[1];
                // poster.style.display="block";

            }); 

            document.onkeydown = checkKey; // to use left and right arrows to change tracks
            function checkKey(e) {
                e = e || window.event;
                if(e.keyCode==37){
                console.log("prev audio track");
                playlist.prev();
                } 
                else if(e.keyCode==39){
                console.log("next audio track");
                playlist.next();
                } 
            } 

        });

    </script>
</x-guest-layout>
