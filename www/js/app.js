(function() {
    var genre = 'indie',
        candidates = { },
        currentSound = null;

    SC.initialize({
        client_id: '4e4115330f6a3238d4631409185ec7a5'
    });

    $('body').on('click', '.vote', vote);
    nextTrack();

    function nextTrack() {
        SC.get('/tracks', { genres: genre, limit: 50 }, function(tracks) {
            candidates['first'] = tracks[Math.floor(Math.random() * tracks.length)];
            candidates['second'] = tracks[Math.floor(Math.random() * tracks.length)];

            $('.first .title').html(candidates['first'].title);
            $('.first img').attr('src', candidates['first'].artwork_url);
            $('.second .title').html(candidates['second'].title);
            $('.second img').attr('src', candidates['second'].artwork_url);
            play('first', function() { play('second'); });
        });
    }

    function play(id, callback) {
        var track = candidates[id];
        $('.' + id + ' img').addClass('playing');
        SC.stream(track.uri, function(sound) {
            currentSound = sound;
            currentSound.play();
            setTimeout(function() {
                stopPlaying();
                callback && callback();
            }, 10000);
        });
    }

    function stopPlaying() {
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
                nextTrack();
            }
        });

        event.preventDefault();
    }

    function showVotes(response) {
        $('.votes').html(response);
    }
})();

