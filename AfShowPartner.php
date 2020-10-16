<?php

namespace AfShowPartner;

use Shopware\Components\Plugin;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Shopware\Components\Plugin\Context\InstallContext;
use Shopware\Components\Plugin\Context\UninstallContext;
use Doctrine\Common\Collections\ArrayCollection;

/** Cookie Stuff */
use Shopware\Bundle\CookieBundle\CookieCollection;
use Shopware\Bundle\CookieBundle\Structs\CookieGroupStruct;
use Shopware\Bundle\CookieBundle\Structs\CookieStruct;


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
            'Theme_Compiler_Collect_Plugin_Less' => 'onCollectLess',
            'CookieCollector_Collect_Cookies' => 'addComfortCookie',
        ];
    }

    //public function addComfortCookie(): CookieCollection
    //{
        //$collection = new CookieCollection();
        //$collection->add(new CookieStruct(
            //'partnertracking',
            //'/^partnertracking$/',
            //'Partneranzeige im Headbereich',
            //CookieGroupStruct::COMFORT
        //));

        //return $collection;
    //}

    public function addComfortCookie(): CookieCollection
    {
        $pluginNamespace = $this->container->get('snippets')->getNamespace('my_plugins_snippet_namespace');

        $collection = new CookieCollection();
        $collection->add(new CookieStruct(
            'allow_local_storage',
            '/^match_no_cookie_DJ7ra9WMy8$/',
            'Statistische Daten in den LocalStorage speichern',
            CookieGroupStruct::COMFORT
        ));

        return $collection;
    }

    public function install(InstallContext $install){
        $service = $this->container->get('shopware_attribute.crud_service');
        $connection = Shopware()->Db();

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

        $table = "CREATE TABLE `af_show_partner`
            ( `id` INT NOT NULL AUTO_INCREMENT , `partnerId` INT NOT NULL , `partnerLink` VARCHAR(255) NOT NULL ,
            `name` VARCHAR(255) NOT NULL, `sessionId` INT NOT NULL,  PRIMARY KEY (`id`)) ENGINE = InnoDB";

        $connection->query($table);
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
        $view->Template()->addPluginsDir($this->getPath(). '/Resources/views/_private/smarty/');

        $partnerLink = $request->getRequestUri();

        if(strpos($partnerLink, '/?sPartner') !== false){
            $partnerCode = str_replace("/?sPartner=", "", $partnerLink);
            $partnerId = $this->checkIfHasName($partnerCode);
            $partnerViewName = $this->getPartnerViewName($partnerId);
            $imageId = $this->getPartnerImage($partnerId);
            $sessionId = Shopware()->Session()->get("sessionId");

            $view->assign('afPartnerName', array(
                'name' => $partnerViewName,
                'image' => $imageId
                )
            );

            $this->writePartner($partnerId['0'], $partnerLink, $partnerViewName, $sessionId);
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

    public function writePartner($partnerId, $partnerLink, $name, $sessionId){
        $connection = Shopware()->Db();

        $select = "SELECT * FROM af_show_partner WHERE sessionId = '".$sessionId."'";
        $hasEntry = $connection->fetchAll($select);

        if(!$hasEntry){
            $insert = "INSERT INTO af_show_partner (id,partnerId, partnerLink, name, sessionId)
                VALUES ( null, '".$partnerId."', '".$partnerLink."', '".$name."', '".$sessionId."')";
            $connection->query($insert);
        }else{
            return;
        }
    }

    public function onCollectLess()
    {
        return new \Shopware\Components\Theme\LessDefinition(
            [], [$this->getPath() . '/Resources/views/frontend/_public/src/less/main.less']
        );
    }
}
