<?php

declare(strict_types=1);

namespace Taxcom\Library\Interface\DTO;

/**
 * Интерфейс объекта перевозчика
 */
interface ShippingDTO
{

    /**
     * Возвращаем ID перевозки
     *
     * @return int|null
     */
    public function getId(): ?int;

    /**
     * Возвращаем экспедитор
     * чекпоинт с экспедитором
     *
     * @return bool|null
     */
    public function getRoot(): ?bool;

    /**
     * Возвращаем название перевозки
     *
     * @return string|null
     */
    public function getName(): ?string;

    /**
     * Возвращаем объект
     * перевозки с данными
     * без экспедитора
     *
     * @return string|null
     */
    public function getJson(): ?string;

    /**
     * Возвращаем объект
     * перевозки с данными
     * с экспедитором
     *
     * @return string|null
     */
    public function getJsonForwarder(): ?string;

    /**
     * Возвращаем дату перевозки
     *
     * @return string|null
     */
    public function getDate(): ?string;

    /**
     * Возвращаем перевозчика
     *
     * @return string|null
     */
    public function getCarrier(): ?string;

    /**
     * Возвращаем ИНН перевозчика
     *
     * @return string|null
     */
    public function getCarrierInn(): ?string;

    /**
     * Возвращаем грузовладельца
     *
     * @return string|null
     */
    public function getCargo(): ?string;

    /**
     * Возвращаем ИНН грузовладельца
     *
     * @return string|null
     */
    public function getCargoInn(): ?string;

    /**
     * Возвращаем экспедитора
     *
     * @return string|null
     */
    public function getForwarder(): ?string;

    /**
     * Возвращаем ИНН экспедитора
     *
     * @return string|null
     */
    public function getForwarderInn(): ?string;

    /**
     * Возвращаем чек-лист подписанных договоров
     *
     * @return string|null
     */
    public function getContractCheck(): ?string;

    /**
     * Возвращаем чек-лист перевозки оформления
     *
     * @return string|null
     */
    public function getDocumentsCheck(): ?string;

    /**
     * Возвращаем чек-лист авто проверки
     *
     * @return string|null
     */
    public function getAutomaticCheck(): ?string;

    /**
     * Возвращаем чек-лист бух документов
     *
     * @return string|null
     */
    public function getAccountingCheck(): ?string;

    /**
     * Возвращаем чек-лист тягача
     *
     * @return string|null
     */
    public function getDonkeyCheck(): ?string;

    /**
     * Возвращаем чек-лист прицепа
     *
     * @return string|null
     */
    public function getTrailerCheck(): ?string;

    /**
     * Возвращаем чек-лист второго прицепа
     *
     * @return string|null
     */
    public function getTrailerSecondaryCheck(): ?string;

    /**
     * Возвращаем чек-лист грузовика
     *
     * @return string|null
     */
    public function getTruckCheck(): ?string;
}