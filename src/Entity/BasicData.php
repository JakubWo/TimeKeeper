<?php

//use DateTime;

class BasicData{

    private string $city;
    private string $street;
    private DateTime $visitDate;
    private string $visitHour;

    /**
     * @return string
     */
    public function getCity(): string
    {
        return $this->city;
    }

    /**
     * @param string $city
     */
    public function setCity(string $city): void
    {
        $this->city = $city;
    }

    /**
     * @return string
     */
    public function getStreet(): string
    {
        return $this->street;
    }

    /**
     * @param string $street
     */
    public function setStreet(string $street): void
    {
        $this->street = $street;
    }

    /**
     * @return DateTime
     */
    public function getVisitDate(): DateTime
    {
        return $this->visitDate;
    }

    /**
     * @param DateTime $visitDate
     */
    public function setVisitDate(DateTime $visitDate): void
    {
        $this->visitDate = $visitDate;
    }

    /**
     * @return string
     */
    public function getVisitHour(): string
    {
        return $this->visitHour;
    }

    /**
     * @param string $visitHour
     */
    public function setVisitHour(string $visitHour): void
    {
        $this->visitHour = $visitHour;
    }


}