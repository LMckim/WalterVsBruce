<?php

class pageBuild{

    public function buildPage($imageDir)
    {
        $card = 'card.html';
        $cardTemplate = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/pages/elements/'.$card);
        $imageDir = array_diff(scandir($imageDir),array('..','.'));
        array_splice($imageDir,sizeof($imageDir)-1);
        $page ='';
        $page .= $this->addHeader();
        $page .= $this->addNav();
        $page .= $this->generateCards($imageDir,$cardTemplate);
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
    private function generateCards($imageDir,$cardTemplate)
    {   
        $content = '';
        foreach($imageDir as $image)
        {
            $newCard = $cardTemplate;
            $imageTitle = substr($image,0,strpos($image,"."));
            $this->insertString_replaceKey($newCard,$imageTitle,'{{title}}');
            $class = 'card-image';
            $imagePath = '../../images/thumbs/' . $image;
            $this->insertImageSource_atClass($newCard,$imagePath,$class);
            $content .= $newCard;

        }
        return $content;

    }
    private function addFooter()
    {
        $footer = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/pages/footer.html');
        return $footer;
    }

    private function insertString_replaceKey(&$baseString,$stringToInsert,$insertionKey)
    {
        $offset = strlen($insertionKey);
        $insertionPoint = strpos($baseString,$insertionKey);
        $baseString = substr_replace($baseString,$stringToInsert,$insertionPoint,$offset);
    }
    private function insertImageSource_atClass(&$baseString,$imageSrc,$class)
    {
        $stringToFind = 'class=' . $class;
        $classPos = strpos($baseString,$stringToFind);
        $srcPos = strpos($baseString,"src=\"\"",$classPos);
        $insertionPoint = $srcPos + strlen("src=\"");
        $baseString = substr_replace($baseString,$imageSrc,$insertionPoint,0);
    }
}
?>