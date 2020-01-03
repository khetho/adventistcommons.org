<?php

namespace App\Form;

use App\Product\AttachmentUploader;
use App\Product\CoverUploader;
use App\Product\IdmlUploader;
use App\Product\PdfDigitalUploader;
use App\Product\PdfOffsetUploader;

class UploaderAggregator
{
    private $uploaders;

    public function __construct(
        AttachmentUploader $attachmentUploader,
        CoverUploader $coverUploader,
        IdmlUploader $idmlUploader,
        PdfDigitalUploader $pdfDigitalUploader,
        PdfOffsetUploader $pdfOffsetUploader
    ) {
        $this->uploaders = [
            $attachmentUploader,
            $coverUploader,
            $idmlUploader,
            $pdfDigitalUploader,
            $pdfOffsetUploader
        ];
    }

    public function upload($data)
    {
        $found = false;
        foreach ($this->uploaders as $uploader) {
            if ($uploader->handle($data)) {
                $data = $uploader->upload($data);
                $found = true;
            }
        }
        
        if (!$found) {
            throw new Exception('Cannot treate file upload', 1);
        }
        
        return $data;
    }
}
