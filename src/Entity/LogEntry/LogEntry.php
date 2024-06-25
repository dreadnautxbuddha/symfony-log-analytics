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
    // phpcs:ignore
    private ?int $id = null;

    #[ORM\Column(length: 32)]
    // phpcs:ignore
    private ?string $service_name = null;

    #[ORM\Column]
    // phpcs:ignore
    private ?\DateTimeImmutable $logged_at = null;

    #[ORM\Column(enumType: RequestMethod::class)]
    // phpcs:ignore
    private ?RequestMethod $http_request_method = null;

    #[ORM\Column(type: Types::TEXT)]
    // phpcs:ignore
    private ?string $http_request_target = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 2, scale: 1)]
    // phpcs:ignore
    private ?string $http_version = null;

    #[ORM\Column(type: Types::SMALLINT)]
    // phpcs:ignore
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
        // phpcs:ignore
        return $this->service_name;
    }

    /**
     * @param string $serviceName
     *
     * @return $this
     */
    public function setServiceName(string $serviceName): static
    {
        // phpcs:ignore
        $this->service_name = $serviceName;

        return $this;
    }

    /**
     * @return \DateTimeImmutable|null
     */
    public function getLoggedAt(): ?\DateTimeImmutable
    {
        // phpcs:ignore
        return $this->logged_at;
    }

    /**
     * @param \DateTimeImmutable $loggedAt
     *
     * @return $this
     */
    public function setLoggedAt(\DateTimeImmutable $loggedAt): static
    {
        // phpcs:ignore
        $this->logged_at = $loggedAt;

        return $this;
    }

    /**
     * @return \Dreadnaut\LogAnalyticsBundle\Enum\Http\RequestMethod|null
     */
    public function getHttpRequestMethod(): ?RequestMethod
    {
        // phpcs:ignore
        return $this->http_request_method;
    }

    /**
     * @param \Dreadnaut\LogAnalyticsBundle\Enum\Http\RequestMethod $httpRequestMethod
     *
     * @return $this
     */
    public function setHttpRequestMethod(RequestMethod $httpRequestMethod): static
    {
        // phpcs:ignore
        $this->http_request_method = $httpRequestMethod;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getHttpRequestTarget(): ?string
    {
        // phpcs:ignore
        return $this->http_request_target;
    }

    /**
     * @param string $httpRequestTarget
     *
     * @return $this
     */
    public function setHttpRequestTarget(string $httpRequestTarget): static
    {
        // phpcs:ignore
        $this->http_request_target = $httpRequestTarget;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getHttpVersion(): ?string
    {
        // phpcs:ignore
        return $this->http_version;
    }

    /**
     * @param string $httpVersion
     *
     * @return $this
     */
    public function setHttpVersion(string $httpVersion): static
    {
        // phpcs:ignore
        $this->http_version = $httpVersion;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getHttpStatusCode(): ?int
    {
        // phpcs:ignore
        return $this->http_status_code;
    }

    /**
     * @param int $httpStatusCode
     *
     * @return $this
     */
    public function setHttpStatusCode(int $httpStatusCode): static
    {
        // phpcs:ignore
        $this->http_status_code = $httpStatusCode;

        return $this;
    }
}
