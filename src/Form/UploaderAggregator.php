<?php

namespace App\Form;

use App\Product\Uploader\AttachmentUploader;
use App\Product\Uploader\CoverUploader;
use App\Product\Uploader\IdmlUploader;
use App\Product\Uploader\PdfDigitalUploader;
use App\Product\Uploader\PdfOffsetUploader;

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
