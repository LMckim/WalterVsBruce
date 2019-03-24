<?php

class pageBuild{

    public function buildPage($imageDir,$attr,$conn)
    {
        $card = 'card.html';
        $cardTemplate = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/pages/elements/'.$card);
        $imageDir = parseDirectory_forFiles($imageDir);
        $page ='';
        $page .= $this->addHeader();
        $page .= $this->addNav($attr);
        $page .= $this->generateCards($imageDir,$cardTemplate,$conn);
        $page .= $this->addFooter();
        return $page;
    }

    private function addHeader()
    {
        $header = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/pages/header.html');
        return $header;
    }
    private function addNav($attr)
    {
        $nav = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/pages/nav-bar.html');
        if(in_array('loggedIn',$attr))
        {   
            $form = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/pages/elements/admin-form-loggedIn.html');
        }else{
            $form = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/pages/elements/admin-form-default.html');
        }
        $this->insertString_atId($nav,$form,'admin-form');
        return $nav;
    }
    private function generateCards($imageDir,$cardTemplate,$conn)
    {   
        $content = '';

        $sql = "SELECT `title`,`path`,`date` FROM `images` LIMIT 50";
        $imageDB = $conn->query($sql);
        if(mysqli_num_rows($imageDB) <= 0)
        {
            print("database query failure,either no entries or some other bigger issue");
        }
        $rows = array();
        while($row = $imageDB->fetch_array(MYSQLI_ASSOC))
        {
            $row['path'] = basename($row['path']);
            $rows[] = $row;
        }
        //get list of images from directory
        $imageDir = array_reverse($imageDir);
        foreach($imageDir as $imageName)
        {
            $newCard = $cardTemplate;
            foreach($rows as $row)
            {
                if(in_array($imageName,$row))
                {
                    $imageTitle = $row['title'];
                    $date = $row['date'];
                    break;
                }else{
                    $imageTitle = 'Bruce_Trail';
                }
            }
            $this->insertString_replaceKey($newCard,$imageTitle,'{{title}}');
            $class = 'card-image';
            $imagePath = '../../images/thumbs/' . $imageName;
            $this->insertImageSource_atClass($newCard,$imagePath,$class);
            $this->insertString_atClass($newCard,$date,'card-date');
            $content .= $newCard;

        }
        return $content;

    }
    private function addFooter()
    {
        $footer = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/pages/footer.html');
        return $footer;
    }

    // string insertion tools
    private function insertString_replaceKey(&$baseString,$stringToInsert,$insertionKey)
    {
        $offset = strlen($insertionKey);
        $insertionPoint = strpos($baseString,$insertionKey);
        $baseString = substr_replace($baseString,$stringToInsert,$insertionPoint,$offset);
    }
    private function insertString_atId(&$baseString,$stringToInsert,$ID)
    {
        $pos = strpos($baseString,$ID);
        $insertionPoint = strpos($baseString,'>',$pos) + 1; // just to move past the '>'
        $baseString = substr_replace($baseString,$stringToInsert,$insertionPoint,0);
    }
    private function insertString_atClass(&$baseString,$stringToInsert,$class)
    {
        $pos = strpos($baseString,$class);
        $insertionPoint = strpos($baseString,'>',$pos) + 1; // just to move past the '>'
        $baseString = substr_replace($baseString,$stringToInsert,$insertionPoint,0);
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