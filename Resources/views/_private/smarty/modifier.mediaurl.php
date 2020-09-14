<?php
function smarty_modifier_mediaurl($value, $format = array())
{
    if(is_numeric($value)){
       $id = (int)$value;
        $media = Shopware()->Models()->getRepository('Shopware\Models\Media\Media')->findOneBy(['id' => $id]);
        if ($media) {
            $path = $media->getPath();

            $mediaUrl = Shopware()->Container()->get('shopware_media.media_service')->getUrl($path);

            return $mediaUrl;
     }
    }
}

