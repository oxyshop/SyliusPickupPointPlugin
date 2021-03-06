<?php

declare(strict_types=1);

namespace Tests\Setono\SyliusPickupPointPlugin\Behat\Page\Shop\ShippingPickup;

use Sylius\Behat\Page\Shop\Checkout\SelectShippingPage as BaseSelectShippingPage;

final class SelectShippingPage extends BaseSelectShippingPage implements SelectShippingPageInterface
{
    public function chooseFirstShippingPointFromDropdown(): void
    {
        $this->getDocument()->waitFor(5, function () {
            return $this->hasElement('pickup_point_dropdown');
        });

        $dropdown = $this->getElement('pickup_point_dropdown');

        $dropdown->click();

        $this->getDocument()->waitFor(5, function () {
            return $this->hasElement('pickup_point_dropdown_item');
        });

        $item = $this->getElement('pickup_point_dropdown_item', [
            '%value%' => 'gls-001',
        ]);

        $item->click();
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'pickup_point_dropdown' => '.setono-sylius-pickup-point-autocomplete.dropdown',
            'pickup_point_dropdown_item' => '.setono-sylius-pickup-point-autocomplete.dropdown .menu .item[data-value="%value%"]',
        ]);
    }
}
