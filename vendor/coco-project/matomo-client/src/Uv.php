<?php

    namespace Coco\matomo;

    class Uv
    {
        private array   $pvs     = [];
        public ?Session $session = null;

        public function __construct(public string $viewTime)
        {

        }

        public function getLastViewTime(): string
        {
            $viewTime = [];
            foreach ($this->pvs as $k => $pv)
            {
                $viewTime[] = strtotime($pv->getForcedDatetime());
            }

            return date('Y-m-d H:i:s', max($viewTime));
        }

        public function getPvs(): array
        {
            return $this->pvs;
        }

        public function getPvCount(): ?int
        {
            return count($this->pvs);
        }

        public function getViewTime(): string
        {
            return $this->viewTime;
        }

        public function setSession(?Session $session): static
        {
            $this->session = $session;

            return $this;
        }

        public function addPv(Pv $pv): static
        {
            $this->pvs[] = $pv;

            return $this;
        }

        public function importPvs(array $pvs): static
        {
            foreach ($pvs as $k => $pv)
            {
                $this->addPv($pv);
            }

            return $this;
        }

        public function makeRequests(int $siteId): array
        {
            $requests = [];

            foreach ($this->pvs as $pv)
            {
                $pv->setSiteId($siteId);

                $res = $this->session->getResolution();
                $pv->setResolution($res[0], $res[1]);
                $pv->setUserAgent($this->session->getUserAgent());
                $pv->setSessionId($this->session->getId());
                $pv->setIp($this->session->getIp());

                $requests[] = $pv->makeUrl();
            }

            return $requests;
        }

        public function makeDataPair(int $siteId): array
        {
            $requests = [];

            foreach ($this->pvs as $pv)
            {
                $pv->setSiteId($siteId);

                $res = $this->session->getResolution();
                $pv->setResolution($res[0], $res[1]);
                $pv->setUserAgent($this->session->getUserAgent());
                $pv->setSessionId($this->session->getId());
                $pv->setIp($this->session->getIp());

                $requests[] = $pv->getTrackerDataArray();
            }

            return $requests;
        }

        public function getSessionId(): string
        {
            return $this->session->getId();
        }
    }

