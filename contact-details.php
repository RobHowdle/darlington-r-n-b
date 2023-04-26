<div class="contacts">
    <button type="button" class="btn btn-primary" data-toggle="collapse" data-target="#contacts">Click Contacts</button>
    <div id="contacts" class="collapse">
        <h2>Location</h2>
        <div class="location">
            <p>
                Contact the box office for further details, ticket prices and future attractions
                01325 363135</p> <br>
            <a href="tel:01325 363135"><i class="fas fa-mobile"></i></a>

            <p class="more">
                <br>The Forum Music Centre
                <br>Borough Road
                <br>Darlington
                <br>Co. Durham
                <br>DL1 1SG
                <br><br>

                <a id="gmaplink" href="https://www.google.com/maps?ll=54.526111,-1.547778&z=18&t=m&hl=en&gl=GB&mapclient=embed&cid=13469663912451722175" target="_blank">Map Link</a>
            </p>

            <a class="read">read more</a>
        </div>
        <div class="bookings">
            <h2>Contacts</h2>
            <p>For booking arrangements and to send Demo Cd's, please contact:</p>
            <div class="more">
                <p>
                    Club Secretary: Ian Ross<br><br>
                    <a href="tel:07855 937357"><i class="fas fa-mobile"></i></a>
                    <a href="mailto:ianross0545@gmail.com"><i class="fas fa-envelope"></i></a>
                    <br><br>
                    Publicity Officer and Master of Ceremonies: Mike Prendergast<br><br>
                    <a href="tel:07905446785"><i class="fas fa-mobile"></i></a>
                    <a href="mailto:gasto.promotions@gmail.com"><i class="fas fa-envelope"></i></a>
                </p>
            </div>
            <a class="read">read more</a>
        </div>

        <div class="audiowrapper">
            <button type="button" id="audiobtn" class="btn btn-primary" data-toggle="collapse" data-target="#plytraks, #mplayer, #selection">Click Media</button>
            <div id="mplayer" class="collapse">
                <article id="plytraks">
                    <!-- <div> -->
                    <p>
                        In the early days of Darlington R’n’B Club, to keep ticket prices down and encourage as many people as possible to attend our gigs, we generated limited runs of
                        souvenir T-shirts and compilation cd’s.
                    </p>
                    <br>
                    <p class="more">
                        We asked the bands who had played for us if we could use one of their tracks from their cd’s to be included on a
                        compilation of limited-edition cd’s. The first cd was released in 1996 and then every two years after that until 2012, the cd costs were kept low as we thought
                        of them as a nice souvenir for our loyal customers who turned up gig after gig and supported our events. <br><br>
                        When we moved from Darlington Arts Centre to the Forum Music Centre, if felt like a good time to move on in general. So we stopped releasing cd’s as music had
                        become more accessible online so it just seemed the right time to move our demo tracks online. With cooperation from the bands we have been able to use their tracks
                        as a demo playlist on our website to highlight the quality bands that Darlington R'n'B Club books for their events.
                    </p>
                    <a class="read">read more</a>
                    <!-- </div> -->
                </article>
                <div class="player">
                    <!-- <audio controls>
                            <source src ="/audio/Roxette.mp3" type="audio/mpeg">
                        </audio> -->
                    <div class="controls autoplay">
                        <!-- duration-->
                        <div class="audio">
                            <span id="audio-currenttime" class="text-center text-white">00:00</span>
                            <span id="audio-duration" class="text-center text-white">00:00</span>
                        </div>
                        <div id="trackbar" class="bar">
                            <div id="fillbar" class="fillbar"></div>
                            <div class="handle"></div>
                        </div>
                        <!-- end duration-->
                        <!---->
                        <div class="buttons">
                            <div class="btns" id="nextbtn"></div>
                            <div class="btns" id="playbtn"></div>
                            <div class="btns" id="pausebtn"></div>
                            <div class="btns" id="stopbtn"></div>
                            <div class="btns" id="prevbtn"></div>
                            <div class="btns" id="vol-up"></div>
                            <div class="btns" id="vol-mute"></div>
                        </div>
                        <!--volume-->
                        <input type="range" id="apvolume" class="slider" min="0" value="50" max="100" step="10">
                    </div>

                    <div class="viswrapper">
                        <div id="songinfo">
                            <div id="artist"></div>
                            <div class="hidden" id="title">Unknown Title</div>
                            <div id="album"></div>
                        </div>
                        <!-- <div id="canvaswrap"> -->
                        <canvas id="visualiser"></canvas>
                        <!-- </div> -->
                    </div>
                </div> <!-- end player div -->
            </div>
        </div> <!--audio wrapper-->
    </div>
</div>