<?php
namespace App\EventSubscriber;

use App\Entity\Post;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Symfony\Component\HttpFoundation\File\File;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

    class ImageCacheSubscriber implements EventSubscriber
    {

        private CacheManager $cacheManager;
        private UploaderHelper $uploaderHelper;

        public function __construct(CacheManager $cacheManager, UploaderHelper $uploaderHelper)
        {
            $this->cacheManager   = $cacheManager;
            $this->uploaderHelper = $uploaderHelper;
        }

        public function getSubscribedEvents()
        {
            return array(
                'preUpdate',
                'preRemove'
            );
        }

        public function preUpdate(PreUpdateEventArgs $args)
        {

            $entity = $args->getEntity();

            if ( ! $entity instanceof Post ) 
            {
                return;
            }

            if ( ! $entity->getImageFile() instanceof File ) 
            {
                return;
            }

            $this->cacheManager->remove($this->uploaderHelper->asset($entity, 'imageFile'));
        }


        public function preRemove(LifecycleEventArgs $args)
        {
            $entity = $args->getEntity();

            if ( ! $entity instanceof Post  ) 
            {
                return;
            }

            $this->cacheManager->remove($this->uploaderHelper->asset($entity, 'imageFile'));
        }
    }