<?php

declare(strict_types=1);

namespace spec\Setono\SyliusPickupPointPlugin\Provider;

use PhpSpec\ObjectBehavior;
use Setono\DAO\Client\ClientInterface;
use Setono\SyliusPickupPointPlugin\Model\PickupPointInterface;
use Setono\SyliusPickupPointPlugin\Provider\DAOProvider;
use Setono\SyliusPickupPointPlugin\Provider\ProviderInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\OrderInterface;

class DAOProviderSpec extends ObjectBehavior
{
    public function let(ClientInterface $client): void
    {
        $this->beConstructedWith($client);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(DAOProvider::class);
    }

    public function it_implements_provider_interface(): void
    {
        $this->shouldImplement(ProviderInterface::class);
    }

    public function it_finds_one_pickup_point(ClientInterface $client): void
    {
        $client->get('/DAOPakkeshop/FindPakkeshop.php', [
            'shopid' => '1234',
        ])->willReturn([
            'resultat' => [
                'pakkeshops' => [$this->pickupPointArray('1234')],
            ],
        ]);

        $pickupPoint = $this->findOnePickupPointById('1234');
        $pickupPoint->shouldBeAnInstanceOf(PickupPointInterface::class);

        $this->testPickupPoint($pickupPoint, '1234');
    }

    public function it_finds_multiple_pickup_points(
        ClientInterface $client,
        OrderInterface $order,
        AddressInterface $address
    ): void {
        $client->get('/DAOPakkeshop/FindPakkeshop.php', [
            'postnr' => 'AE 8000',
            'adresse' => 'Street 10',
            'antal' => 10,
        ])->willReturn([
            'resultat' => [
                'pakkeshops' => [$this->pickupPointArray('0'), $this->pickupPointArray('1')],
            ],
        ]);

        $address->getPostcode()->willReturn('AE 8000');
        $address->getStreet()->willReturn('Street 10');

        $order->getShippingAddress()->willReturn($address);

        $pickupPoints = $this->findPickupPoints($order);
        $pickupPoints->shouldHaveCount(2);
        $pickupPoints->shouldBeArrayOfPickupPoints();

        for ($i = 0; $i < 2; ++$i) {
            $this->testPickupPoint($pickupPoints[$i], (string) $i);
        }
    }

    public function getMatchers(): array
    {
        return [
            'beArrayOfPickupPoints' => static function ($pickupPoints) {
                foreach ($pickupPoints as $element) {
                    if (!$element instanceof PickupPointInterface) {
                        return false;
                    }
                }

                return true;
            },
        ];
    }

    private function testPickupPoint($pickupPoint, string $id): void
    {
        $pickupPoint->getId()->shouldReturn($id);
        $pickupPoint->getName()->shouldReturn('Mediabox');
        $pickupPoint->getAddress()->shouldReturn('Bilka Vejle 20');
        $pickupPoint->getZipCode()->shouldReturn('7100');
        $pickupPoint->getCity()->shouldReturn('Vejle');
        $pickupPoint->getCountry()->shouldReturn('DK');
        $pickupPoint->getProviderCode()->shouldReturn('dao');
        $pickupPoint->getLatitude()->shouldReturn('55.7119');
        $pickupPoint->getLongitude()->shouldReturn('9.539939');
    }

    private function pickupPointArray(string $id): array
    {
        return [
            'shopId' => $id,
            'navn' => 'Mediabox',
            'adresse' => 'Bilka Vejle 20',
            'postnr' => '7100',
            'bynavn' => 'Vejle',
            'udsortering' => 'E',
            'latitude' => '55.7119',
            'longitude' => '9.539939',
            'afstand' => 2.652,
            'aabningstider' => [
                'man' => '08:00 - 22:00',
                'tir' => '08:00 - 22:00',
                'ons' => '08:00 - 22:00',
                'tor' => '08:00 - 22:00',
                'fre' => '08:00 - 24:00',
                'lor' => '10:00 - 24:00',
                'son' => '10:00 - 22:00',
            ],
        ];
    }
}
