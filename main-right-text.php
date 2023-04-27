<?php include('variable_content.php'); ?>

<div class="right-col">
    <h2 class="sub-title">Location</h2>
    <p>Contact the box office for further details, ticket prices and future attractions
        <div class="link">
            <a href="tel:01325363135" class="hover-underline">
                <span class="fal fa-phone-alt" aria-hidden="true"></span>
                <div>01325 363135</div>
            </a>

            <a class="hover-underline" href="https://goo.gl/maps/fPTZx8RrVLgwXitb6" target="_blank">
                <span class="fas fa-map-marker-alt" aria-hidden="true"></span>
                <div><?php echo nl2br($locationAddress); ?></div>
            </a>
        </div>
    </p>

    <div class="gmap">
        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d1157.7122941783125!2d-1.5485747024770649!3d54.5260086468717!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x487e9bce8fb0f8f5%3A0xbaeddbf689954bbf!2sThe%20Forum%20Music%20Centre!5e0!3m2!1sen!2suk!4v1575473424666!5m2!1sen!2suk"></iframe>
    </div>

    <h2 class="sub-title">Contacts</h2>
    <p>For booking arrangements and to send Demo Cd's, please contact:</p>
    <div class="contact-wrapper">
        <div class="contact">
            <p>Ian Ross - Club Secretary</p>
            <div class="contact-links">
                <a class="hover-underline" href="tel:07855937357">
                    <span class="fal fa-phone-alt" aria-hidden="true"></span>
                    <div>Call Me</div>
                </a>

                <a class="hover-underline" href="mailto:ianross0545@gmail.com">
                    <span class="fal fa-envelope" aria-hidden="true"></span>
                    <div>Email Me</div>
                </a>
            </div>
        </div>

        <div class="contact">
            <p>Mike Prendergast - Publicity Officer and Master of Ceremonies</p>
            <div class="contact-links">
                <a class="hover-underline" href="tel:07905446785">
                    <span class="fal fa-phone-alt" aria-hidden="true"></span>
                    <div>Call Me</div>
                </a>

                <a class="hover-underline" href="mailto:gasto.promotions@gmail.com">
                    <span class="fal fa-envelope" aria-hidden="true"></span>
                    <div>Email Me</div>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- <div class="contacts">
    <button type="button" class="btn btn-primary" data-toggle="collapse" data-target="#contacts">Click Contacts</button>
    <div id="contacts" class="collapse">

        <div class="audiowrapper">
            <button type="button" id="audiobtn" class="btn btn-primary" data-toggle="collapse" data-target="#plytraks, #mplayer, #selection">Click Media</button>
            <div id="mplayer" class="collapse">
                <article id="plytraks">
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
                </article>
                <div class="player">
                    <div class="controls autoplay">
                        <div class="audio">
                            <span id="audio-currenttime" class="text-center text-white">00:00</span>
                            <span id="audio-duration" class="text-center text-white">00:00</span>
                        </div>
                        <div id="trackbar" class="bar">
                            <div id="fillbar" class="fillbar"></div>
                            <div class="handle"></div>
                        </div>
                        <div class="buttons">
                            <div class="btns" id="nextbtn"></div>
                            <div class="btns" id="playbtn"></div>
                            <div class="btns" id="pausebtn"></div>
                            <div class="btns" id="stopbtn"></div>
                            <div class="btns" id="prevbtn"></div>
                            <div class="btns" id="vol-up"></div>
                            <div class="btns" id="vol-mute"></div>
                        </div>
                        <input type="range" id="apvolume" class="slider" min="0" value="50" max="100" step="10">
                    </div>

                    <div class="viswrapper">
                        <div id="songinfo">
                            <div id="artist"></div>
                            <div class="hidden" id="title">Unknown Title</div>
                            <div id="album"></div>
                        </div>
                        <canvas id="visualiser"></canvas>
                    </div>
                </div>
        </div>
    </div>
</div> -->
