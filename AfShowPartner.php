<?php

namespace AfShowPartner;

use Shopware\Components\Plugin;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Shopware\Components\Plugin\Context\InstallContext;
use Shopware\Components\Plugin\Context\UninstallContext;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Shopware-Plugin AfShowPartner.
 */
class AfShowPartner extends Plugin
{

    /**
    * @param ContainerBuilder $container
    */
    public function build(ContainerBuilder $container)
    {
        $container->setParameter('af_show_partner.plugin_dir', $this->getPath());
        parent::build($container);
    }

    public static function getSubscribedEvents(){
        return[
            'Enlight_Controller_Action_PostDispatch_Frontend' => 'onFrontend',
            'Theme_Compiler_Collect_Plugin_Javascript' => 'onCollectJs',
        ];
    }

    public function install(InstallContext $install){
        $service = $this->container->get('shopware_attribute.crud_service');

        $service->update('s_emarketing_partner_attributes', 'afpartnernameactive', 'boolean', [
            'label' => 'Aktivieren?',
            'supportText' => 'Aktiviere Partneranzeige',
            'position' => -110,
            'displayInBackend' => true
        ]);

        $service->update('s_emarketing_partner_attributes', 'afpartnername', 'text', [
            'label' => 'Parter Name',
            'supportText' => 'Zeigt den Namen des Partners wenn sein Link bentutz wurde',
            'helpText' => 'Zeigt den Namen des Partners wenn sein Link bentutz wurde',
            'position' => -100,
            'displayInBackend' => true
        ]);

        $service->update(
          's_emarketing_partner_attributes',
          'afpartnerimage',
          \Shopware\Bundle\AttributeBundle\Service\TypeMapping::TYPE_SINGLE_SELECTION,
          [
            'entity'           => \Shopware\Models\Media\Media::class,
            'label'            => 'Zusatzbild für Hersteller',
            'displayInBackend' => true,
            'supportText'      => 'zusätzliches Bild',
            'translatable'     => false,
          ]
        );
    }

    public function uninstall(UninstallContext $install){
        $service = $this->container->get('shopware_attribute.crud_service');
        $service->delete('s_emarketing_partner_attributes', 'afpartnerimage');
    }

    public function onFrontend(\Enlight_Event_EventArgs $args){
        $controller = $args->getSubject();
        $request = $args->getRequest();
        $view = $controller->View();
        $view->addTemplateDir($this->getPath() . "/Resources/views/");


        // TODO: Read the cookie to get the partner name - so the
        // banner always shows the partner information
        // This doesnt work - cauz secured
        // dumP($controller->Request()->cookies->parameters);
        // another way would be to go over localstorage


        $partnerLink = $request->getRequestUri();
        if(strpos($partnerLink, '/?sPartner') !== false){
            $partnerCode = str_replace("/?sPartner=", "", $partnerLink);
            $partnerId = $this->checkIfHasName($partnerCode);
            $partnerViewName = $this->getPartnerViewName($partnerId);
            $imageId = $this->getPartnerImage($partnerId);
            $view->assign('afPartnerName', array(
                'name' => $partnerViewName,
                'image' => $imageId
            )
            );
        }
    }

    public function checkIfHasName($partnerCode){
        $connection = Shopware()->Db();
        $getPartner = "SELECT id FROM s_emarketing_partner WHERE idcode = '".$partnerCode."'";
        $partnerId = $connection->fetchCol($getPartner);

        return $partnerId;
    }

    public function getPartnerViewName($partnerId){
        $connection = Shopware()->Db();
        $getViewName = "SELECT afpartnername FROM s_emarketing_partner_attributes WHERE partnerID = '".$partnerId['0']."'";
        $name = $connection->fetchCol($getViewName);

        return $name['0'];
    }

    public function getPartnerImage($partnerId){
        $connection = Shopware()->Db();
        $getImage = "SELECT afpartnerimage FROM s_emarketing_partner_attributes WHERE partnerID = '".$partnerId['0']."'";
        $imageId = $connection->fetchCol($getImage);

        return $imageId['0'];
    }

    public function onCollectJs(){
        $collection = new ArrayCollection();
        $collection->add($this->getPath() . '/Resources/views/frontend/_public/src/js/main.js');

        return $collection;
    }
}
