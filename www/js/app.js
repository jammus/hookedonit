(function() {
    var genre = $(document.body).data('genre'),
        candidates = { },
        currentSound = null,
        playTimeout = null;

    SC.initialize({
        client_id: '4e4115330f6a3238d4631409185ec7a5'
    });

    var lastfm = new LastFM({
        apiKey: 'b91ccadc8e59cee1bbf7c86ed0d6a70c'
    });

    $('body').on('click', '.vote', vote);

    $('select').change(function() {
        $(this).parent('form').submit();
    });

    loadTracks();

    function loadTracks() {
        $('.track img').attr('src', '');
        SC.get('/tracks', { genres: genre, limit: 50 }, function(tracks) {
            candidates['first'] = tracks[Math.floor(Math.random() * tracks.length)];
            candidates['second'] = tracks[Math.floor(Math.random() * tracks.length)];
            $('.first .title').html(candidates['first'].title);
            $('.second .title').html(candidates['second'].title);
            loadImage('first');
            loadImage('second');
            play('first', function() { play('second'); });
        });
    }

    function loadImage(id) {
        var imageUrl = candidates[id].artwork_url || '/img/no-art.svg';
        $('.' + id + ' img').attr('src', imageUrl);
    }

    function play(id, callback) {
        var track = candidates[id];
        $('.' + id + ' img').addClass('playing');
        SC.stream(track.uri, function(sound) {
            currentSound = sound;
            currentSound.play();
            playTimeout = setTimeout(function() {
                stopPlaying();
                callback && callback();
            }, 15000);
        });
    }

    function stopPlaying() {
        clearTimeout(playTimeout);
        currentSound && currentSound.stop();
        currentSound = null;
        $('.track img').removeClass('playing');
    }

    function vote(event) {
        stopPlaying();
        var element = $(event.currentTarget),
            id = element.data('track'),
            track = candidates[id];

        $.ajax('/vote', {
            type: 'POST',
            data: {
                id: track.id,
                uri: track.uri,
                title: track.title,
                genre: genre
            }, 
            success: function(response) {
                showVotes(response);
                loadTracks();
            }
        });

        event.preventDefault();
    }

    function showVotes(response) {
        $('.results').html(response);
    }
})();

