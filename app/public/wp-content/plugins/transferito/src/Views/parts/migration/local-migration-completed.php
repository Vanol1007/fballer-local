<?php echo loadTemplate( 'parts/notice', [
    'title'             => 'Upload Completed',
    'image'             => 'completed-migration',
    'type'              => 'success',
    'message'           => "We're happy to let you know that your upload to our secure servers has completed. You are ready to install your Wordpress Site on your local environment.",
    'additionalInfo'    => 'To continue your migration, open Transferito Desktop',
    'externalLink'      => [
        'anchorText'    => 'Download Transferito Desktop',
        'linkURL'       => 'https://transferito.com/download/desktop'
    ],
    'extraInfo'         => [
        'title'     => 'Liked Using our Plugin?',
        'content'   => 'If you did, please <a target="_blank" class="transferito-log-event" data-event-name="leaveReview" href="https://wordpress.org/support/plugin/transferito/reviews/">click here</a> to leave us a review. Your feedback is very important to us & helps us to continually improve our plugin.'
    ]
]); ?>
