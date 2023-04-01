<!DOCTYPE html>
<html>
    <head>
        <?= $this->load->view('layouts/components/head', '', TRUE) ?>
        <title>TrackStreet</title>
        <meta name="description" content="TrackStreet">
        <meta name="keywords" content="TrackStreet">
    </head>
    <body>
        <div id="page">
            <main role="main">
                <div class="container">
                    <?= $content ?>
                </div>
            </main>
        </div><!-- #page -->
        <?= $this->load->view('layouts/components/javascript', '', TRUE) ?>
    </body>
</html>
