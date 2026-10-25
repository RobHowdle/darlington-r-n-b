<style>
    /* Keep this page-specific carousel aligned with the original artwork-first layout. */
    .audio-track-slider .swiper-slide {
        max-width: none;
        width: auto;
        align-content: start;
    }

    .audio-track-slider .swiper-slide > img {
        display: block;
        width: 100%;
        max-width: none;
        max-height: none;
        aspect-ratio: 1 / 1;
        object-fit: cover;
    }

    .audio-track-slider .swiper-slide > .text-center {
        padding: 0.75rem 0.25rem 0;
    }

    .audio-track-slider .slider-buttons-wrapper {
        position: relative;
        display: flex;
        align-items: center;
        gap: 1rem;
        height: 35px;
        margin-top: 0.25rem;
    }

    .audio-track-slider .slider-buttons-wrapper .swiper-button-prev,
    .audio-track-slider .slider-buttons-wrapper .swiper-button-next {
        position: static !important;
        inset: auto;
        flex: 0 0 35px;
        width: 35px;
        height: 35px;
        margin: 0;
        transform: none;
    }
</style>

<section class="home-grid-section">
    <div class="res--140">
        <div class="inner-wrapper--1680">
            <div class="audio-container">
                <h1 class="blue-ds pb-1 text-center">Audio</h1>
                <p class="text-center">In the mood for some music? Try our custom music player!</p>
                <div class="audio-grid mt-2">
                    <div class="player">
                        <div class="controls autoplay">
                            <div class="buttons">
                                <img src="media/img/stop.png" id="stopBtn" alt="Stop">
                                <img src="media/img/prev.png" id="prevBtn" alt="Previous track">
                                <img src="media/img/next.png" id="nextBtn" alt="Next track">
                                <img src="media/img/play.png" id="playBtn" alt="Play or pause">
                            </div>
                        </div>
                        <audio id="audioPlayer" preload="none"></audio>
                        <div class="record-player">
                            <img src="media/img/record-player.png" alt="">
                            <div class="overlay">
                                <div class="viswrapper"><canvas id="visualiser"></canvas></div>
                            </div>
                        </div>
                    </div>

                    <div class="slider swiper audio-track-slider" style="margin-left:0!important;margin-right:0!important;">
                        <div class="swiper-wrapper">
                            @forelse ($tracks as $index => $track)
                                <div class="swiper-slide" data-track-index="{{ $index }}" data-track-id="{{ $track['id'] }}">
                                    <img src="{{ $track['coverUrl'] }}" alt="{{ $track['coverAlt'] }}" loading="lazy">
                                    <div class="text-center">
                                        <p class="slider-overlay--main">{{ $track['title'] }}</p>
                                        <p class="slider-overlay--small blue-ds">{{ $track['artist'] }}</p>
                                    </div>
                                </div>
                            @empty
                                <div class="swiper-slide audio-empty">
                                    <p>No tracks are available at the moment.</p>
                                </div>
                            @endforelse
                        </div>
                        @if ($tracks->isNotEmpty())
                            <div class="slider-buttons-wrapper">
                                <div class="swiper-button-prev"><i class="fas fa-chevron-left"></i></div>
                                <div class="swiper-button-next"><i class="fas fa-chevron-right"></i></div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const playlist = @js($tracks->values());
    const audioPlayer = document.getElementById('audioPlayer');
    const playButton = document.getElementById('playBtn');
    const stopButton = document.getElementById('stopBtn');
    const nextButton = document.getElementById('nextBtn');
    const prevButton = document.getElementById('prevBtn');
    const canvas = document.getElementById('visualiser');
    const slides = [...document.querySelectorAll('.audio-track-slider .swiper-slide[data-track-index]')];
    let currentTrackIndex = 0;
    let audioContext;
    let analyser;
    let animationFrame;

    if (!playlist.length) {
        [playButton, stopButton, nextButton, prevButton].forEach(button => button?.setAttribute('aria-disabled', 'true'));
        return;
    }

    const slider = window.Swiper ? new Swiper('.audio-track-slider', {
        slidesPerView: 1.5,
        spaceBetween: 30,
        loop: playlist.length > 1,
        navigation: {nextEl: '.swiper-button-next', prevEl: '.swiper-button-prev'},
    }) : null;

    function markPlaying(index) {
        slides.forEach(slide => slide.classList.toggle('playing', Number(slide.dataset.trackIndex) === index));
    }

    function playTrack(index) {
        currentTrackIndex = (index + playlist.length) % playlist.length;
        audioPlayer.src = playlist[currentTrackIndex].audioUrl;
        markPlaying(currentTrackIndex);
        audioPlayer.play().catch(() => {});
    }

    playButton.addEventListener('click', () => {
        if (audioPlayer.paused) {
            if (!audioPlayer.src) playTrack(currentTrackIndex);
            else audioPlayer.play().catch(() => {});
        } else {
            audioPlayer.pause();
        }
    });
    nextButton.addEventListener('click', () => playTrack(currentTrackIndex + 1));
    prevButton.addEventListener('click', () => playTrack(currentTrackIndex - 1));
    stopButton.addEventListener('click', () => {
        audioPlayer.pause();
        audioPlayer.currentTime = 0;
    });
    audioPlayer.addEventListener('ended', () => playTrack(currentTrackIndex + 1));

    slides.forEach(slide => slide.addEventListener('click', () => {
        const index = Number(slide.dataset.trackIndex);
        playTrack(index);
        slider?.slideToLoop(index);
    }));

    if (canvas) {
        const context = canvas.getContext('2d');
        const wrapper = canvas.closest('.viswrapper');
        const draw = () => {
            if (!analyser) return;
            const width = wrapper.clientWidth;
            const height = wrapper.clientHeight;
            canvas.width = width;
            canvas.height = height;
            const values = new Uint8Array(analyser.frequencyBinCount);
            analyser.getByteFrequencyData(values);
            context.clearRect(0, 0, width, height);
            const cx = width / 2;
            const cy = height / 2;
            const maxRadius = Math.max(0, Math.min(cx, cy) - 10);
            values.forEach(value => {
                context.beginPath();
                context.arc(cx, cy, (value / 255) * maxRadius, 0, 2 * Math.PI);
                context.strokeStyle = `rgba(55, 120, 255, ${value / 255})`;
                context.lineWidth = 2;
                context.stroke();
            });
            animationFrame = requestAnimationFrame(draw);
        };
        audioPlayer.addEventListener('play', () => {
            if (!audioContext) {
                audioContext = new AudioContext();
                analyser = audioContext.createAnalyser();
                analyser.fftSize = 256;
                audioContext.createMediaElementSource(audioPlayer).connect(analyser);
                analyser.connect(audioContext.destination);
            }
            audioContext.resume();
            cancelAnimationFrame(animationFrame);
            draw();
        });
        audioPlayer.addEventListener('pause', () => cancelAnimationFrame(animationFrame));
    }
});
</script>
