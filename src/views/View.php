<?php

namespace phpessential\mvc\views;

abstract class View {
    private $clientMessages = array();
    private $clientRoutes = array();

    protected function writeHtmlMeta() {

    }

    protected function writeHtmlScripts() {

    }

    protected function writeHtmlLinks() {

    }

    protected abstract function getTitle();

    public function putClientMessage(string $key, string $message) {
        $this->clientMessages[$key] = $message;
    }

    public function putClientRoute(string $key, string $route) {
        $this->clientRoutes[$key] = $route;
    }

    public function putClientMessages(array $messages) {
        $this->clientMessages = $this->clientMessages + $messages;
    }

    public function putClientRoutes(array $routes) {
        $this->clientRoutes = $this->clientRoutes + $routes;
    }

    private function writeClientRoutes() {
        $jsRoutes = 'var jsRoutes={};';
        foreach ($this->clientRoutes as $key => $route) {
            $jsRoutes .= "jsMessages.$key=$route;";
        }
        echo $jsRoutes;
    }

    private function writeClientMessages() {
        $jsMessages = 'var jsMessages={};';
        foreach ($this->clientMessages as $key => $message) {
            $jsMessages .= "jsMessages.$key=$message;";
        }
        echo $jsMessages;
    }

    private function writeHtmlHeader() {
        ?>
        <html lang="fr" class="no-js">
            <head>
                <?php
                $this->writeHtmlMeta();
                $this->writeHtmlLinks();
                ?>
                <title><?php echo $this->getTitle(); ?></title>
            </head>
            <?php
        }

        private function writeHtmlFooter() {
            ?>

            <script src="<?php echo Route::createUrlFromRoot("vendor/fortawesome/font-awesome/js/all.min.js"); ?>" type="text/javascript"></script>
            <script src="<?php echo Route::createUrlFromRoot("vendor/components/jquery/jquery.min.js"); ?>" type="text/javascript"></script>
            <script src="<?php echo Route::createUrlFromRoot("vendor/twbs/bootstrap/dist/js/bootstrap.bundle.min.js"); ?>" type="text/javascript"></script>
            <script type="text/javascript">
        <?php
        $this->writeClientRoutes();
        $this->writeClientMessages();
        ?>
            </script>
            <?php
            $this->writeHtmlScripts();
            ?>
        </html>
        <?php
    }

    protected abstract function writeHtmlBody();

    public function render() {
        $this->writeHtmlHeader();
        $this->writeHtmlBody();
        $this->writeHtmlFooter();
    }

}
