<?php

namespace App\Form;

use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\Form\Event\PostSubmitEvent;
use Symfony\Component\Form\Event\PreSubmitEvent;

class FormListenerFactory
{
    public function autoSlug(string $field): callable
    {
        return function (PreSubmitEvent $event) use ($field) {

            $data = $event->getData();
            if (empty($data[$field])) {
                $slugger = new AsciiSlugger();
                $data['slug'] = strtolower($slugger->slug($data[$field]));
                $event->setData($data);
            }
        };
    }

    public function attachTimestamps(): callable
    {
        return function (PostSubmitEvent $event) {
            $entity = $event->getData();

            $entity->setUpdatedAt(new \DateTimeImmutable());
            if (!$entity->getId()) {
                $entity->setCreatedAt(new \DateTimeImmutable());
            }
        };
    }
}
