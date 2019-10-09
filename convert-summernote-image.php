    //GET THE BODY
    $body = $_POST['body'];
    
    // GET IMAGES
    $documentModel = new DOMDocument();
    $documentModel->loadHTML($body);
    $xpath = new DOMXPath($documentModel);
    $image_list = $xpath->query("//img[@src]");
    
    if ($image_list->length > 0) {
        $adminId = $_SESSION['admin']['id_row'];
        $targetDir = "./uploads/email/$adminId";
        if (!is_dir($targetDir)) {
            mkdir($targetDir);
        }
        //  ITERATE THROUGH IMAGES IF ANY
        for($i=0;$i<$image_list->length; $i++){
            
            $image['src'] = $image_list->item($i)->getAttribute("src");
            $image['data-filename'] = $image_list->item($i)->getAttribute("data-filename");
            $base64Array = explode(',', $image['src'], 2);
            $base64code = $base64Array[1];
            $base64decode = base64_decode($base64code);
    
            if (strlen($base64code) > 5000000) { //LIMIT IMAGE TO 5 MB
               // HANDLE SIZE IMAGE ERROR
            } else {
                $imageFromString = imageCreateFromString($base64decode);
                if ($imageFromString) {
                
                    $fileName = $image['data-filename'];
                    $imgFullPath = __DIR__ . '/' . $targetDir . '/' . $fileName;
                    $createFile = imagepng($imageFromString, $imgFullPath, 0);
                    $fileUrl = URL . 'uploads/email/'. $adminId . '/' . $fileName;
                    
                    if ($createFile) {
                        $body = str_replace($image['src'], $fileUrl, $body);
                        $body = str_replace($image['src'], $fileUrl, $body);
                    } else {
                        // HANDLE ERROR CREATING FILE
                    }
                } else {
                    // HANDLE ERROR CONVERTING IMAGE
                    
                }
            }
        }
        
        unset($image_list, $xpath, $documentModel);
    }
