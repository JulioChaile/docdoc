<?php

namespace common\components;

use Exception;
use Yii;
use yii\base\Component;
use Google_Client;
use Google_Service_Calendar;
use Google_Service_Calendar_Calendar;
use Google_Service_Calendar_CalendarListEntry;
use Google_Service_Calendar_AclRule;
use Google_Service_Calendar_AclRuleScope;
use Google_Service_Calendar_Event;
use Google_Service_Calendar_EventReminder;
use Google_Service_Calendar_EventReminders;
use Google_Service_Calendar_EventAttendee;
use Google_Service_Calendar_ColorDefinition;
use Google_Service_Exception;

class Calendar extends Component
{
    private $key = '{
      "type": "service_account",
      "project_id": "docdoc-265916",
      "private_key_id": "5309af82e0bc4d771662ae0fa932b0572ee9649c",
      "private_key": "-----BEGIN PRIVATE KEY-----\nMIIEvgIBADANBgkqhkiG9w0BAQEFAASCBKgwggSkAgEAAoIBAQDqwssVAAp/ZAxf\nhPaGRBE9iwSVKMh0R/bZRbZoeId1VFjpP3E7/IiiMISlfaru+nS7lZ96dwdql+gK\nrwYiM60NlVmCB/g5QA/BiD3XIpRDkIB2kx+4vnWuSa122mfZ7R1tjo5UDXXK7mxh\nrRZu7KHWS/Ow6aua14ZCanHvmtEmoA+L0VlaZZwYYECKT8dEmwzEJPCd4/8+RjaS\nHlA1antdzOygFhsyYGMZy/MSE2fE3yDb8syUv8X9ll/w6pflgaTuywC/d3OJSsC8\n6Umcz0+PjjWErpacq9lQxDLg2161+qyvv7R3G3vbpXJQSG6ULMSEjvZ88DuzyjMZ\n39qItB6RAgMBAAECggEAU96qugJtPazLJNb2UeqAdEm1pepPjwdkv6PBspoY3sh3\nCUGSnTkvwS3vPcZjKoSM7rVaJ+DdY+4IRsTXvqFSmm84bpWVTzK9TklzumfOq0K1\nOmd+ZjyZA16sG7GUd41YPZs36vxyEEFUtCKnyJI+kTZKRfJ9TdDg1Np9gPoA7biC\n/tGVNjUNM+2sjBvPI8JArElQoT2JZv3l3py1Hq7hIgXSUjuDKQbxcFFBwIFR6l8G\nol3MLcny9j38uBt+dJMXO3JH9gVH2PzVTR5kUZKgm5SFtGCfRfRES27rMxoXRFPT\nkJ7NE3mKKE2WTXWZ41SP5NR0DOywg94lE8neROKTpwKBgQD9+ohMLm7syQde1uhy\nm2ITieTqCh9i2V791zDy+ug0kjYB+tdOD2SVfDLgODLD4eXKCuu858x0JrXkdOFo\nHrDwaEb0hYAbN1WOeCHytJggErwy0UGdXBCxyuOZRe/1SRvJ9BafCqjlMT3Xwe1F\nZMBjUIXW7I62jnZeHOSuWEg2dwKBgQDsoRsVzQQ7m6ftozIc5cfrC7+ei983h2mk\naYd62uRrwLdeuAJ4LLVJHcBptPKVYXlUs7WMfXrNzMkVldkxFoL9SNDdWB0Z37lq\n80lWnL1dJ/NGEitkA+acmb9Pd8ZkJSYhxHEccK2BFBVR3gQMB+XioOqt5DPo/6n9\nzheEnS6tNwKBgQD3JnjCIaFiHNJmUR3MgTa0qsivk4AtcjhFLsZ8fPvARNP3o0En\nvkT0TvM3TJjiE47IyU3T+4HzOcRhd/ftmYg3ulHqG4upcHR6ep8WjvVGqNSpYwbF\n+dRpH3XSLsOu3yECqtvkkrv+pKd4sUeS8tNhEffcSUErl4DKXrWOj2xeSwKBgAJD\njFHKE1dKpvGkFQ+ntyDtjNjEd889MWqMQ+qN+494WYjDc+qYaueXLEcWnxeExjdk\nPMFqVelwIyBcvaY1k+0+bBkiBa1AsbJvP21ftIQWpMIv3FBppSQsaGMnPzOoE1RR\nX8+o2FAa1BVjbWB8FtvzNCuTuldpUsQF272+DztDAoGBAMcK5zwyf6xPPsu+AqSq\ndTNLctgV8Dr2csTjOrnNpc0il8/VSX4KnlzbiDiaT4IVenLi1sQuGJdlTOPk35Q8\nRVUy/CTPhis2IOrx89T8N/RkpU9vJzhfY9KcRc/JeKdNQ8BYprqMS6lYktFV9m90\nJnSyj4Q20JoTIesK3zPLnFYV\n-----END PRIVATE KEY-----\n",
      "client_email": "calendar-docdoc@docdoc-265916.iam.gserviceaccount.com",
      "client_id": "111288232335637158004",
      "auth_uri": "https://accounts.google.com/o/oauth2/auth",
      "token_uri": "https://oauth2.googleapis.com/token",
      "auth_provider_x509_cert_url": "https://www.googleapis.com/oauth2/v1/certs",
      "client_x509_cert_url": "https://www.googleapis.com/robot/v1/metadata/x509/calendar-docdoc%40docdoc-265916.iam.gserviceaccount.com"
    }';
    public $googleClient;
    public $CalendarService;
    public $TimeZone = 'America/Argentina/Buenos_Aires';
    // public $Subject = 'contacto@docdoc.com.ar';

    /**
     * Inicializa el cliente de Google Calendar
     *
     */
    public function __construct($Subject = '')
    {
        $this->googleClient = new Google_Client();

        $this->googleClient->setApplicationName("DocDoc! Calendario");
        $this->googleClient->setAuthConfig(json_decode($this->key, true));
        $this->googleClient->setScopes(Google_Service_Calendar::CALENDAR);
        if (!empty($Subject)) {
          $this->googleClient->setSubject($Subject);
        }
        
        $this->CalendarService = new Google_Service_Calendar($this->googleClient);

        parent::__construct();
    }

    public function insertAcl($calendarId, $scopeValue = '', $scopeType = 'user', $role = 'writer')
    {
      $rule = new Google_Service_Calendar_AclRule();
      $scope = new Google_Service_Calendar_AclRuleScope();

      $scope->setType($scopeType);
      $scope->setValue($scopeValue);

      $rule->setScope($scope);
      $rule->setRole($role);

      $createdRule = $this->CalendarService->acl->insert($calendarId, $rule);

      return $createdRule->getId();
    }

    public function updateAcl($calendarId, $ruleId, $role)
    {
      $rule = $this->CalendarService->acl->get($calendarId, $ruleId);
      $rule->setRole($role);

      $updatedRule = $this->CalendarService->acl->update($calendarId, $rule->getId(), $rule);

      return $updatedRule->getId();
    }

    public function deleteAcl($calendarId, $ruleId)
    {
      $deletedRule = $this->CalendarService->acl->delete($calendarId, $ruleId);

      return $deletedRule;
    }

    public function insertCalendar($Title, $Description = '', $colorId = '', $location = '')
    {
        $calendar = new Google_Service_Calendar_Calendar();

        $calendar->setSummary($Title);
        $calendar->setDescription($Description);
        $calendar->setLocation($location);
        $calendar->setTimeZone($this->TimeZone);

        $createdCalendar = $this->CalendarService->calendars->insert($calendar);

        if (!empty($colorId)) {
          $calendarListEntry = new Google_Service_Calendar_CalendarListEntry();
          $calendarListEntry->setId($createdCalendar->getId());
          $calendarListEntry->setColorId($colorId);

          $createdCalendarListEntry = $this->CalendarService->calendarList->insert($calendarListEntry);
        }

        return $createdCalendar->getId();
    }

    public function updateCalendar($calendarId, $newTitle = null, $newDescription = null, $newColorId = null, $newLocation = null, $newTimeZone = null)
    {
        $calendar = $this->CalendarService->calendars->get($calendarId);

        $oldTitle = $calendar->getSummary();
        $oldDescription = $calendar->getDescription();
        $oldLocation = $calendar->getLocation();
        $oldTimeZone = $calendar->getTimeZone();

        $calendar->setSummary($newTitle ?? $oldTitle);
        $calendar->setDescription($newDescription ?? $oldDescription);
        $calendar->setLocation($newLocation ?? $oldLocation);
        $calendar->setTimeZone($newTimeZone ?? $oldTimeZone);

        if ($newColorId !== null) {
          $calendarListEntry = $this->CalendarService->calendarList->get($calendarId);
          $calendarListEntry->setColorId($newColorId);

          $updatedCalendarListEntry = $this->CalendarService->calendarList->update($calendarListEntry->getId(), $calendarListEntry);
        }

        $updateCalendar = $this->CalendarService->calendars->update($calendarId, $calendar);

        return $updateCalendar->getId();
    }

    public function deleteCalendar($calendarId) 
    {
      $deletedCalendar = $this->CalendarService->calendars->delete($calendarId);

      return $deletedCalendar;
    }

    public function insertEvent($Title = '', $Description = '', $Attendees = array(), $start, $end, $calendarId, $colorId = '', $location = '')
    {
      $event = new Google_Service_Calendar_Event(array(
          'summary' => $Title,
          'location' => $location,
          'description' => $Description,
          'start' => array(
            'dateTime' => $start,
            'timeZone' => $this->TimeZone,
          ),
          'end' => array(
            'dateTime' => $end,
            'timeZone' => $this->TimeZone,
          )
      ));

      if (!empty($colorId)) {
        $event->setColorId($colorId);
      }

      $remindersArray = array();

      $reminder = new Google_Service_Calendar_EventReminder();
      $reminder->setMethod('email');
      $reminder->setMinutes(24*60);
      $remindersArray[] = $reminder;
      
      $reminder = new Google_Service_Calendar_EventReminder();
      $reminder->setMethod('popup');
      $reminder->setMinutes(30);
      $remindersArray[] = $reminder;
      
      $reminders = new Google_Service_Calendar_EventReminders();
      $reminders->setUseDefault(false);
      $reminders->setOverrides($remindersArray);

      $event->setReminders($reminders);
      
      $attendees = array();
      
      foreach ($Attendees as $a) {
        $attendee = new Google_Service_Calendar_EventAttendee();
        $attendee->setEmail($a);
        $attendees[] = $attendee;
      }

      $event->attendees = $attendees;
      
      $createdEvent = $this->CalendarService->events->insert($calendarId, $event, array('sendNotifications' => true));

      return [
        'Id' => $createdEvent->getId(),
        'Link' => $createdEvent->getHtmlLink()
      ];
    }

    public function updateEvent($eventId, $calendarId, $newTitle = null, $newDescription = null, $newStart = null, $newEnd = null, $newColorId = null, $newLocation = null, $newMinutesEmail = null, $newMinutesPopup = null, $newTimeZone = null)
    {
      $event = $this->CalendarService->events->get($calendarId, $eventId);

      $oldTitle = $event->getSummary();
      $oldDescription = $event->getDescription();
      $oldStart = $event->getStart()->dateTime;
      $oldEnd = $event->getEnd()->dateTime;
      $oldColorId = $event->getColorId();
      $oldLocation = $event->getLocation();
      $oldTimeZone = $event->getStart()->timeZone;

      $event->setSummary($newTitle ?? $oldTitle);
      $event->setDescription($newDescription ?? $oldDescription);
      $event->setStart(array(
        'dateTime' => $newStart ?? $oldStart,
        'timeZone' => $newTimeZone ?? $oldTimeZone,
      ));
      $event->setEnd(array(
        'dateTime' => $newEnd ?? $oldEnd,
        'timeZone' => $newTimeZone ?? $oldTimeZone,
      ));
      $event->setColorId($newColorId ?? $oldColorId);
      $event->setLocation($newLocation ?? $oldLocation);

      $remindersArray = array();

      if ($newMinutesEmail !== null) {
        $reminder = new Google_Service_Calendar_EventReminder();
        $reminder->setMethod('email');
        $reminder->setMinutes($newMinutesEmail);
        $remindersArray[] = $reminder;
      }
      
      if ($newMinutesPopup !== null) {
        $reminder = new Google_Service_Calendar_EventReminder();
        $reminder->setMethod('popup');
        $reminder->setMinutes($newMinutesPopup);
        $remindersArray[] = $reminder;
      }
      
      if (!empty($remindersArray)) {
        $reminders = new Google_Service_Calendar_EventReminders();
        $reminders->setUseDefault(false);
        $reminders->setOverrides($remindersArray);

        $event->setReminders($reminders);
      }
      
      $updatedEvent = $this->CalendarService->events->insert($calendarId, $eventId, $event, array('sendNotifications' => true));

      return [
        'Id' => $updatedEvent->getId(),
        'Link' => $updatedEvent->getHtmlLink()
      ];
    }

    public function deleteEvent($calendarId, $eventId)
    {
      $deletedEvent = $this->CalendarService->events->delete($calendarId, $eventId);

      return $deletedEvent;
    }

    public function getColors()
    {
      $colors = $this->CalendarService->colors->get();

      return [
        'calendar' => $colors->getCalendar(),
        'event' => $colors->getEvent()
      ];
    }
}
