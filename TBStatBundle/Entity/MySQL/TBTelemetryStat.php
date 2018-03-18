<?php

namespace TBStatBundle\Entity\MySQL;

use Doctrine\ORM\Mapping as ORM;
use TBStatBundle\Entity\BaseTelemetryEntity;

/**
 * TBTelemetryStat
 *
 * @ORM\Table(name="tb_telemetry_stat")
 * @ORM\Entity(repositoryClass="TBStatBundle\Repository\MySQL\TBTelemetryStatRepository")
 */
class TBTelemetryStat extends BaseTelemetryEntity
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="day_ts", type="integer")
     */
    private $day_ts;

    /**
     * @var string
     *
     * @ORM\Column(name="tb_id", type="string", length=48)
     */
    private $tb_id;

    /**
     * @var string
     *
     * @ORM\Column(name="tb_name", type="string", length=255)
     */
    private $tb_name;

    /**
     * @var string
     *
     * @ORM\Column(name="value_name", type="string", length=48)
     */
    private $value_name;

    /**
     * @var float
     *
     * @ORM\Column(name="value", type="float")
     */
    private $value;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set dayTs
     *
     * @param integer $dayTs
     *
     * @return TBTelemetryStat
     */
    public function setDayTs($dayTs)
    {
        $this->day_ts = $dayTs;

        return $this;
    }

    /**
     * Get dayTs
     *
     * @return int
     */
    public function getDayTs()
    {
        return $this->day_ts;
    }

    /**
     * Set tbId
     *
     * @param string $tbId
     *
     * @return TBTelemetryStat
     */
    public function setTbId($tbId)
    {
        $this->tb_id = $tbId;

        return $this;
    }

    /**
     * Get tbId
     *
     * @return string
     */
    public function getTbId()
    {
        return $this->tb_id;
    }

    /**
     * Set tbName
     *
     * @param string $tbName
     *
     * @return TBTelemetryStat
     */
    public function setTbName($tbName)
    {
        $this->tb_name = $tbName;

        return $this;
    }

    /**
     * Get tbName
     *
     * @return string
     */
    public function getTbName()
    {
        return $this->tb_name;
    }

    /**
     * Set valueName
     *
     * @param string $valueName
     *
     * @return TBTelemetryStat
     */
    public function setValueName($valueName)
    {
        $this->value_name = $valueName;

        return $this;
    }

    /**
     * Get valueName
     *
     * @return string
     */
    public function getValueName()
    {
        return $this->value_name;
    }

    /**
     * Set value
     *
     * @param float $value
     *
     * @return TBTelemetryStat
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return float
     */
    public function getValue()
    {
        return $this->value;
    }
}

