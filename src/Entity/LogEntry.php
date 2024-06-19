<?php

namespace App\Entity;

use App\Enum\Http\RequestMethod;
use App\Repository\LogEntryRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LogEntryRepository::class)]
class LogEntry
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
    private ?string $http_protocol_version = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $http_status_code = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getServiceName(): ?string
    {
        return $this->service_name;
    }

    public function setServiceName(string $service_name): static
    {
        $this->service_name = $service_name;

        return $this;
    }

    public function getLoggedAt(): ?\DateTimeImmutable
    {
        return $this->logged_at;
    }

    public function setLoggedAt(\DateTimeImmutable $logged_at): static
    {
        $this->logged_at = $logged_at;

        return $this;
    }

    public function getHttpRequestMethod(): ?RequestMethod
    {
        return $this->http_request_method;
    }

    public function setHttpRequestMethod(RequestMethod $http_request_method): static
    {
        $this->http_request_method = $http_request_method;

        return $this;
    }

    public function getHttpRequestTarget(): ?string
    {
        return $this->http_request_target;
    }

    public function setHttpRequestTarget(string $http_request_target): static
    {
        $this->http_request_target = $http_request_target;

        return $this;
    }

    public function getHttpProtocolVersion(): ?string
    {
        return $this->http_protocol_version;
    }

    public function setHttpProtocolVersion(string $http_protocol_version): static
    {
        $this->http_protocol_version = $http_protocol_version;

        return $this;
    }

    public function getHttpStatusCode(): ?int
    {
        return $this->http_status_code;
    }

    public function setHttpStatusCode(int $http_status_code): static
    {
        $this->http_status_code = $http_status_code;

        return $this;
    }
}
