<?php

namespace JW\Mealie\Calendar;

use DateTimeImmutable;
use Illuminate\Support\Collection;
use Eluceo\iCal\Domain\Entity\Event;
use Eluceo\iCal\Domain\Entity\Calendar;
use Eluceo\iCal\Presentation\Component;
use Eluceo\iCal\Domain\ValueObject\DateTime;
use Eluceo\iCal\Domain\ValueObject\TimeSpan;
use Eluceo\iCal\Presentation\Factory\CalendarFactory;

class Mealplan
{

    public function generateCalendar(Collection $collection)
    {
        $this->renderCalendarHeaders();
        return $this->renderCalendar($collection);

    }

    public function renderCalendar(Collection $response): Component
    {
        $events = [];
        foreach ($response as $date => $recipes) {
            $events[] = $this->makeEvent($recipes, $date);
        }
        
        $calendar = new Calendar($events);
        $componentFactory = new CalendarFactory();
        $calendarComponent = $componentFactory->createCalendar($calendar);


        return $calendarComponent;
    }

    public function renderCalendarHeaders()
    {
        header('Content-Type: text/calendar; charset=utf-8');
        header('Content-Disposition: attachment; filename="cal.ics"');

    }

    protected function makeEvent(Collection $data, string $date): Event
    {
        $event = new Event();
        $event
            ->setSummary($this->getEventSummary($data))
            ->setDescription($this->getEventDescription($data))
            ->setOccurrence(
                new TimeSpan(
                    new DateTime(DateTimeImmutable::createFromFormat('Y-m-d H:i', $date . ' 17:00'), true),
                    new DateTime(DateTimeImmutable::createFromFormat('Y-m-d H:i', $date . ' 18:00'), true)
                )
            );
        return $event;

    }

    protected function getEventSummary($data)
    {
        $summary = '';
        foreach ($data as $recipe) {
            if($this->isRecipe($recipe)) {
                $summary = $recipe['recipe']['name'];
            } else {
                $summary = $recipe['title'];
            }

            $summary .= ', ';
        }
        $summary = rtrim($summary, ', ');

        return $summary;
    }

    protected function getEventDescription($data)
    {
        $description = '';

        foreach ($data as $recipe) {
            if ($this->isRecipe($recipe)) {
                $description .= '<a href="' . $this->getRecipeUrl($recipe['recipe']['slug']) . '">' . $recipe['recipe']['name'] . '</a><br />';
            } else {
                $description .= $recipe['title'] . '<br />';
            }
        }
        
        return $description;
    }

    protected function isRecipe($recipe)
    {
        if($recipe['recipeId'] == null) {
            return false;
        }

        return true;
    }

    protected function getRecipeUrl($slug): string
    {
        return config('mealie.host') . '/recipe/' . $slug;
    }

}