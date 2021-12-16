/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.css';
require('bootstrap/dist/js/bootstrap');

// start the Stimulus application
import './bootstrap';
import $ from 'jquery';
 
 


var $container = $('.js-votes');
$container.find('a').on('click', function(e) {
    e.preventDefault();
    var $link = $(e.currentTarget);
 
    $.ajax({
        url: '/usermovies/' +  slug + '/' +$link.data('score'),
        method: 'POST'
    }).then(function(data) {
        $container.find('.js-user-rating').text(data.movieRating);
        $container.find('.vote-buttons').remove();

    });
});