<?php

class pageBuild{

    public function buildPage($mageDir)
    {
        $page ='';
        $page .= $this->addHeader();
        $page .= $this->addNav();
        $page .= $this->generateCards($imageDir);
        $page .= $this->addFooter();
        return $page;
    }

    private function addHeader()
    {
        $header = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/pages/header.html');
        return $header;
    }
    private function addNav()
    {
        $nav = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/pages/nav-bar.html');
        return $nav;
    }
    private  function generateCards($imageDir)
    {
    
        return $content;

    }
    private function addFooter()
    {
        $footer = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/pages/footer.html');
        return $footer;
    }
}
?>