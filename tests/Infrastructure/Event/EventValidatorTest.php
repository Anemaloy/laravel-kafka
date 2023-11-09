<?php

declare(strict_types=1);

namespace Anemaloy\KafkaLocator\Tests\Infrastructure\Event;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Anemaloy\KafkaLocator\Domain\EventInterface;
use Anemaloy\KafkaLocator\Infrastructure\Event\EventBuilder;
use Anemaloy\KafkaLocator\Infrastructure\Event\EventValidator;
use Anemaloy\KafkaLocator\Infrastructure\Schema\SchemaManager;
use Anemaloy\KafkaLocator\Tests\TestEventsTrait;

class EventValidatorTest extends TestCase
{
    use TestEventsTrait;

    /**
     * @dataProvider provideEvents
     */
    public function testValidate(EventInterface $event, int $maxErrors, int $expectedErrorsCount): void
    {
        $schemaManager = new SchemaManager();
        $schemaManager->registerPath($this->getEventDirectory(), $this->getEventUriPrefix());
        $sut = new EventValidator($schemaManager);

        $result = $sut->validate($event, $maxErrors);
        self::assertFalse($result->isValid());

        self::assertNotNull($validationResult = $result->getValidationResult());
        self::assertSame($expectedErrorsCount, $validationResult->totalErrors());

        $errors = [[]];
        foreach ($validationResult->getErrors() as $error) {
            $errors[] = [$error, ...$error->subErrors()];
        }
        self::assertCount($expectedErrorsCount, array_merge(...$errors));
    }

    public function provideEvents(): iterable
    {
        $eventBuilder = new EventBuilder();

        $payload = $this->getPayload();

        $event = $eventBuilder->build(
            'test/mainsite/apartment/created',
            '1.1.0',
            new DateTimeImmutable(),
            $payload
        );

        yield [$event, 1, 1];
        yield [$event, 2, 2];
        yield [$event, -1, 7];
        yield [$event, 0, 1];
    }

    private function getPayload(): array
    {
        $json = /* @lang JSON */
            <<<'JSON'
        {
            "uuid": "0a3b7b4d-670d-48e6-9281-3e0afc115167",
            "amoId": "123456789",
            "status": 123,
            "dealType": 456,
            "address": {
              "countryCode": "RU",
              "postalCode": "119019",
              "formatted": "Москва, улица Новый Арбат, 24",
              "components": [
                {
                  "kind": "country",
                  "name": "Россия",
                  "uuid": "330015d4-09ed-4854-aded-4b8c94d17962",
                  "parentUuid": null,
                  "createdAt": "2021-02-10T21:18:49.000+03:00",
                  "updatedAt": "2021-02-10T21:18:49.000+03:00"
                },
                {
                  "kind": "province",
                  "name": "Центральный федеральный округ",
                  "uuid": "630c3759-d955-4c44-8977-e47ff4997c9c",
                  "parentUuid": "330015d4-09ed-4854-aded-4b8c94d17962",
                  "createdAt": "2021-02-10T21:19:49.000+03:00",
                  "updatedAt": "2021-02-10T21:19:49.000+03:00"
                },
                {
                  "kind": "province",
                  "name": "Москва",
                  "uuid": "630c3759-d955-4c44-8977-e47ff4997c9c",
                  "parentUuid": "630c3759-d955-4c44-8977-e47ff4997c9c",
                  "createdAt": "2021-02-10T21:20:49.000+03:00",
                  "updatedAt": "2021-02-10T21:20:49.000+03:00"
                },
                {
                  "kind": "locality",
                  "name": "Москва",
                  "uuid": "7a81c861-3ae2-42af-9331-b7364a7b591e",
                  "parentUuid": "630c3759-d955-4c44-8977-e47ff4997c9c",
                  "createdAt": "2021-02-10T21:21:49.000+03:00",
                  "updatedAt": "2021-02-10T21:21:49.000+03:00"
                },
                {
                  "kind": "street",
                  "name": "улица Новый Арбат",
                  "uuid": "f0c2e578-45e3-4a71-8536-3ae5db5d6a6c",
                  "parentUuid": "7a81c861-3ae2-42af-9331-b7364a7b591e",
                  "createdAt": "2021-02-11T21:18:49.000+03:00",
                  "updatedAt": "2021-02-11T21:18:49.000+03:00"
                },
                {
                  "kind": "house",
                  "name": "24"
                }
              ],
              "point": {
                "latitude": 55.753083,
                "longitude": 37.587614
              }
            },
            "number": 6789,
            "highwayDistance": 111,
            "subwayStations": [],
            "videos": [],
            "images": [],
            "cadastralNumber": null,
            "roominess" : {
              "isStudio": false,
              "count": 3,
              "area": null
            },
            "balconiesCount": 1,
            "loggiasCount": 0,
            "combinedWcsCount": 1,
            "separateWcsCount": 0,
            "floorNumber": 1,
            "fullArea": 25000,
            "livingArea": null,
            "kitchensArea": null,
            "balconiesAndLoggiasArea": null,
            "bedroomsArea": null,
            "bathroomsArea": null,
            "wcsArea": null,
            "hallwaysArea": null,
            "onlineShow": true,
            "priceRub": 5000000,
            "buildingSeries": "Вишневый сад",
            "buildingConstructionYear": 2025,
            "buildingConstructionYearComment": "",
            "buildingFloorCount": 10,
            "buildingCeilingHeight": null,
            "buildingPassengerElevatorsCount": 2,
            "buildingFreightElevatorsCount": 2,
            "buildingPorchesCount": 3,
            "isBuildingHasGarbageChute": true,
            "buildingType": {
              "uuid": "bb82ecf0-6fac-428c-85db-c43607a663d4",
              "name": "Кирпичный",
              "slug": "kirpichniy",
              "altTitle": "Кирпичный Альт",
              "createdAt": "2021-02-11T21:21:49.000+03:00",
              "updatedAt": "2021-02-11T21:21:49.000+03:00"
            },
            "buildingParkingType": {
              "uuid": "af5ea63b-ebe5-4b2a-b891-32f391e0739f",
              "name": "Наземная",
              "slug": "nazemnaya",
              "altTitle": "Наземная Альт",
              "createdAt": "2021-02-11T21:21:49.000+03:00",
              "updatedAt": "2021-02-11T21:21:49.000+03:00"
            },
            "buildingDeveloper": {
              "uuid": "712463ed-ccc5-47aa-8a93-f6065b39d08b",
              "name": "ПИК",
              "slug": "pik",
              "altTitle": "ПИК Альт",
              "createdAt": "2021-02-11T21:21:49.000+03:00",
              "updatedAt": "2021-02-11T21:21:49.000+03:00"
            },
            "housingComplex": {
              "uuid": "6820a35d-c396-4184-98a7-c32981aabe01",
              "name": "Вишневый сад",
              "slug": "vishnevyj-sad",
              "altTitle": "Вишневый сад Альт",
              "createdAt": "2021-02-11T21:21:49.000+03:00",
              "updatedAt": "2021-02-11T21:21:49.000+03:00"
            },
            "housingComplexBuilding": {
              "uuid": "dcccdafd-fa65-43dc-b9c4-f14c1cd0bed4",
              "name": "Сочный",
              "slug": "sochnyj",
              "altTitle": "Сочный Альт",
              "housingComplex": {
                "uuid": "6820a35d-c396-4184-98a7-c32981aabe01",
                "name": "Вишневый сад",
                "altTitle": "Вишневый сад Альт",
                "slug": null,
                "createdAt": "2021-02-11T21:21:49.000+03:00",
                "updatedAt": "2021-02-11T21:21:49.000+03:00"
              },
              "createdAt": "2021-02-11T21:21:49.000+03:00",
              "updatedAt": "2021-02-11T21:21:49.000+03:00"
            },
            "saleTypes": [
              {
                "uuid": "eb1c430d-e94e-42ab-8f5c-0ffd637f2736",
                "name": "Трейд ин+",
                "slug": "tradein+",
                "altTitle": "Трейд ин+ Альт",
                "createdAt": "2021-02-11T21:21:49.000+03:00",
                "updatedAt": "2021-02-11T21:21:49.000+03:00"
              }
            ],
            "housingType": {
              "uuid": "48eaf732-2b2f-4f6a-85a5-14f65115de40",
              "name": "Первичка",
              "isRequiredDeveloper": false,
              "slug": "pervichka",
              "altTitle": "Первичка Альт",
              "createdAt": "2021-02-11T21:21:49.000+03:00",
              "updatedAt": "2021-02-11T21:21:49.000+03:00"
            },
            "decorationType": {
              "uuid": "caf17af5-8abd-4688-8667-afcd2adb9c61",
              "name": "Без ремонта",
              "slug": "bez-remonta",
              "altTitle": "Без ремонта Альт",
              "createdAt": "2021-02-11T21:21:49.000+03:00",
              "updatedAt": "2021-02-11T21:21:49.000+03:00"
            },
            "heatingType": {
              "uuid": "3f67367f-e31c-44f5-a3f6-b18990a71737",
              "name": "Центральное",
              "slug": "central",
              "altTitle": "Центральное Альт",
              "createdAt": "2021-02-11T21:21:49.000+03:00",
              "updatedAt": "2021-02-11T21:21:49.000+03:00"
            },
            "roomType": {
              "uuid": "55acb098-ac97-4e5f-8c4f-6114872361a0",
              "name": "Изолированная",
              "slug": "izolirovannaya",
              "altTitle": "Изолированная Альт",
              "createdAt": "2021-02-11T21:21:49.000+03:00",
              "updatedAt": "2021-02-11T21:21:49.000+03:00"
            },
            "stoveType": {
              "uuid": "990b9c2b-e176-4dea-bf0a-1ef4c3c8dc44",
              "name": "Газ",
              "slug": "gaz",
              "altTitle": "Газ Альт",
              "createdAt": "2021-02-11T21:21:49.000+03:00",
              "updatedAt": "2021-02-11T21:21:49.000+03:00"
            },
            "windowViewType": {
              "uuid": "5a98cc7b-b1f3-4bdb-b8b8-f6d5b2a400ee",
              "name": "На улицу",
              "slug": "na-ulicu",
              "altTitle": "На улицу Альт",
              "createdAt": "2021-02-11T21:21:49.000+03:00",
              "updatedAt": "2021-02-11T21:21:49.000+03:00"
            },
            "createdAt": "2021-02-13T21:18:49.000+03:00",
            "updatedAt": "2021-02-13T21:18:49.000+03:00",
            "contactPhoneNumber": "+74951234567",
            "contactPhoneNumberCode": "+7",
            "isRedevelopment": false
        }
        JSON;

        return ['apartment' => json_decode($json, true, 512, JSON_THROW_ON_ERROR)];
    }
}
