<div class="swiper archive-slider">
    <h4 class="sub-title">Gig Archive</h4>
    <div class="swiper-wrapper">
        <?php foreach($dirs as $archiveElement) { ?>
            <a href="archive/<?php echo $archiveElement; ?>/<?php echo $archiveElement; ?>.php" class="swiper-slide">
                <?php 
                    $images = scandir("archive/".$archiveElement);
                    if ($images !== false) {
                        foreach($images as $image) {
                            if (preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $image)) {
                                $featuredImage = '<img src="archive/'.$archiveElement.'/'.$image.'" />';
                                break; // stop searching after finding the first image
                            }
                        }
                    } else {
                        echo '<p>No images found in '.$archiveElement.'</p>';
                    }
                ?>
                <p class="year"><?php echo $archiveElement; ?></p>
                <div class="image-wrapper">
                    <?php echo $featuredImage; ?>
                </div>
            </a>
        <?php } ?>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.js"></script>

<script>
        let archiveSlider = new Swiper('.archive-slider', {
        slidesPerView: 4,
        spaceBetween: 10,
        loop: true,
        autoplay: {
            pauseOnMouseEnter: true,
            disableOnInteraction: true,
        },
    });
</script>

<!-- 
<section class="archive">
    <button type="button" class="btn btn-primary" data-toggle="collapse" data-target="#archive">Click Archive</button>
    <article id="archive" class="collapse">
        <h2>Gig Archive</h2>
        <ul id="gadates">
            <li>
                <h3><a class="gadate" id="2020" href="archive/GA2020.html">2020</a></h3>
            </li>
            <li>
                <h3><a class="gadate" id="2019" href="archive/GA2019.html">2019</a></h3>
            </li>
            <li>
                <h3><a class="gadate" id="2018" href="archive/GA2018.html">2018</a></h3>
            </li>
            <li>
                <h3><a class="gadate" id="2017" href="archive/GA2017.html">2017</a></h3>
            </li>
            <li>
                <h3><a class="gadate" id="2016" href="archive/GA2016.html">2016</a></h3>
            </li>
            <li>
                <h3><a class="gadate" id="2015" href="archive/GA2015.html">2015</a></h3>
            </li>
            <li>
                <h3><a class="gadate" id="2014" href="archive/GA2014.html">2014</a></h3>
            </li>
            <li>
                <h3><a class="gadate" id="2013" href="archive/GA2013.html">2013</a></h3>
            </li>
            <li>
                <h3><a class="gadate" id="2012" href="archive/GA2012.html">2012</a></h3>
            </li>
            <li>
                <h3><a class="gadate" id="2011" href="archive/GA2011.html">2011</a></h3>
            </li>
            <li>
                <h3><a class="gadate" id="2010" href="archive/GA2010.html">2010</a></h3>
            </li>
            <li>
                <h3><a class="gadate" id="2009" href="archive/GA2009.html">2009</a></h3>
            </li>
            <li>
                <h3><a class="gadate" id="2008" href="archive/GA2008.html">2008</a></h3>
            </li>
            <li>
                <h3><a class="gadate" id="2007" href="archive/GA2007.html">2007</a></h3>
            </li>
            <li>
                <h3><a class="gadate" id="2006" href="archive/GA2006.html">2006</a></h3>
            </li>
            <li>
                <h3><a class="gadate" id="2005" href="archive/GA2005.html">2005</a></h3>
            </li>
            <li>
                <h3><a class="gadate" id="2004" href="archive/GA2004.html">2004</a></h3>
            </li>
            <li>
                <h3><a class="gadate" id="2003" href="archive/GA2003.html">2003</a></h3>
            </li>
            <li>
                <h3><a class="gadate" id="2002" href="archive/GA2002.html">2002</a></h3>
            </li>
            <li>
                <h3><a class="gadate" id="2001" href="archive/GA2001.html">2001</a></h3>
            </li>
            <li>
                <h3><a class="gadate" id="2000" href="archive/GA2000.html">2000</a></h3>
            </li>
            <li>
                <h3><a class="gadate" id="1999" href="archive/GA1999.html">1999</a></h3>
            </li>
            <li>
                <h3><a class="gadate" id="1998" href="archive/GA1998.html">1998</a></h3>
            </li>
            <li>
                <h3><a class="gadate" id="1997" href="archive/GA1997.html">1997</a></h3>
            </li>
            <li>
                <h3><a class="gadate" id="1996" href="archive/GA1996.html">1996</a></h3>
            </li>
            <li>
                <h3><a class="gadate" id="1995" href="archive/GA1995.html">1995</a></h3>
            </li>
        </ul>
    </article>
</section> -->
