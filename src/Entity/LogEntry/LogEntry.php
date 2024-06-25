<?php

namespace Dreadnaut\LogAnalyticsBundle\Entity\LogEntry;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Dreadnaut\LogAnalyticsBundle\Entity\Support\Contracts\EntityInterface;
use Dreadnaut\LogAnalyticsBundle\Enum\Http\RequestMethod;
use Dreadnaut\LogAnalyticsBundle\Repository\LogEntryRepository;

#[ORM\Entity(repositoryClass: LogEntryRepository::class)]
class LogEntry implements EntityInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 32)]
    private ?string $service_name = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $logged_at = null;

    #[ORM\Column(enumType: RequestMethod::class)]
    private ?RequestMethod $http_request_method = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $http_request_target = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 2, scale: 1)]
    private ?string $http_version = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $http_status_code = null;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getServiceName(): ?string
    {
        return $this->service_name;
    }

    /**
     * @param string $service_name
     *
     * @return $this
     */
    public function setServiceName(string $service_name): static
    {
        $this->service_name = $service_name;

        return $this;
    }

    /**
     * @return \DateTimeImmutable|null
     */
    public function getLoggedAt(): ?\DateTimeImmutable
    {
        return $this->logged_at;
    }

    /**
     * @param \DateTimeImmutable $logged_at
     *
     * @return $this
     */
    public function setLoggedAt(\DateTimeImmutable $logged_at): static
    {
        $this->logged_at = $logged_at;

        return $this;
    }

    /**
     * @return \Dreadnaut\LogAnalyticsBundle\Enum\Http\RequestMethod|null
     */
    public function getHttpRequestMethod(): ?RequestMethod
    {
        return $this->http_request_method;
    }

    /**
     * @param \Dreadnaut\LogAnalyticsBundle\Enum\Http\RequestMethod $http_request_method
     *
     * @return $this
     */
    public function setHttpRequestMethod(RequestMethod $http_request_method): static
    {
        $this->http_request_method = $http_request_method;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getHttpRequestTarget(): ?string
    {
        return $this->http_request_target;
    }

    /**
     * @param string $http_request_target
     *
     * @return $this
     */
    public function setHttpRequestTarget(string $http_request_target): static
    {
        $this->http_request_target = $http_request_target;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getHttpVersion(): ?string
    {
        return $this->http_version;
    }

    /**
     * @param string $http_version
     *
     * @return $this
     */
    public function setHttpVersion(string $http_version): static
    {
        $this->http_version = $http_version;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getHttpStatusCode(): ?int
    {
        return $this->http_status_code;
    }

    /**
     * @param int $http_status_code
     *
     * @return $this
     */
    public function setHttpStatusCode(int $http_status_code): static
    {
        $this->http_status_code = $http_status_code;

        return $this;
    }
}
