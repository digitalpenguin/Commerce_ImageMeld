<?php
namespace DigitalPenguin\Commerce_ImageMeld\Fields;
use modmore\Commerce\Exceptions\ViewException;
use modmore\Commerce\Order\Field\AbstractField;

/**
 * Class OrderItemMeld
 *
 * Renders a view of an image.
 * @package DigitalPenguin\Commerce_ImageMeld\Fields
 */
class OrderItemMeld extends AbstractField {

    /**
     * @return string
     */
    public function renderForAdmin(): string
    {
        try {
            return $this->commerce->view()->render('imagemeld/fields/orderitemmeld.twig', [
                'name' => $this->name,
                'value' => $this->value,
            ]);
        } catch (ViewException $e) {
            $this->commerce->adapter->log(1, '[' . __CLASS__ . '] ViewException rendering imagemeld/fields/orderitemmeld.twig: ' . $e->getMessage());
            return 'Error rendering field.';
        }
    }
}