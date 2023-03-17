<?php

namespace App\Entity;

use App\Repository\OpeningHoursRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OpeningHoursRepository::class)]
class OpeningHours
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $day_of_week = null;

    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $lunch_open_time = null;

    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $lunch_close_time = null;

    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dinner_open_time = null;

    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dinner_close_time = null;

    #[ORM\Column]
    private ?bool $is_closed = null;

    #[ORM\OneToMany(mappedBy: 'openingHours', targetEntity: Booking::class, orphanRemoval: true)]
    private Collection $bookings;

    #[ORM\ManyToOne(targetEntity: Restaurant::class, inversedBy: 'openingHours')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Restaurant $restaurant = null;


    public function __construct()
    {
        $this->bookings = new ArrayCollection();
        $this->restaurant = new Restaurant();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDayOfWeek(): ?string
    {
        return $this->day_of_week;
    }

    public function setDayOfWeek(string $day_of_week): self
    {
        $this->day_of_week = $day_of_week;

        return $this;
    }

    public function getLunchOpenTime(): ?\DateTimeInterface
    {
        return $this->lunch_open_time;
    }

    public function setLunchOpenTime(?\DateTimeInterface $lunch_open_time): self
    {
        $this->lunch_open_time = $lunch_open_time;

        return $this;
    }

    public function getLunchCloseTime(): ?\DateTimeInterface
    {
        return $this->lunch_close_time;
    }

    public function setLunchCloseTime(?\DateTimeInterface $lunch_close_time): self
    {
        $this->lunch_close_time = $lunch_close_time;

        return $this;
    }

    public function getDinnerOpenTime(): ?\DateTimeInterface
    {
        return $this->dinner_open_time;
    }

    public function setDinnerOpenTime(?\DateTimeInterface $dinner_open_time): self
    {
        $this->dinner_open_time = $dinner_open_time;

        return $this;
    }

    public function getDinnerCloseTime(): ?\DateTimeInterface
    {
        return $this->dinner_close_time;
    }

    public function setDinnerCloseTime(?\DateTimeInterface $dinner_close_time): self
    {
        $this->dinner_close_time = $dinner_close_time;

        return $this;
    }

    public function isIsClosed(): ?bool
    {
        return $this->is_closed;
    }

    public function setIsClosed(bool $is_closed): self
    {
        $this->is_closed = $is_closed;

        return $this;
    }

    /**
     * @return Collection<int, Booking>
     */
    public function getBookings(): Collection
    {
        return $this->bookings;
    }

    public function addBooking(Booking $booking): self
    {
        if (!$this->bookings->contains($booking)) {
            $this->bookings->add($booking);
            $booking->setOpeningHours($this);
        }

        return $this;
    }

    public function removeBooking(Booking $booking): self
    {
        if ($this->bookings->removeElement($booking)) {
            // set the owning side to null (unless already changed)
            if ($booking->getOpeningHours() === $this) {
                $booking->setOpeningHours(null);
            }
        }

        return $this;
    }

    public function getRestaurant(): ?Restaurant
    {
        return $this->restaurant;
    }

    public function setRestaurant(?Restaurant $restaurant): self
    {
        $this->restaurant = $restaurant;

        return $this;
    }


}
