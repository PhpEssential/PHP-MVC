<?php

namespace phpessential\mvc\utils;

/**
 * Classe utilitaire pour la gestion des dates
 */
class DateUtils {
    const SQL_DATE_FORMAT = "Y-m-d";
    const SQL_DATE_TIME_FORMAT = "Y-m-d H:i:s";
    const DISPLAY_DATE_FORMAT = "d/m/Y";
    const DISPLAY_DATE_TIME_FORMAT = "d/m/Y H:i:s";

    public static function getStartOfWeek(\DateTime $date): \DateTime {
        return clone $date->modify(('Sunday' == $date->format('l')) ? 'Monday last week' : 'Monday this week');
    }

    public static function getEndOfWeek(\DateTime $date) {
        return clone $date->modify('Sunday this week');
    }

    public static function getDaysOfWeek(): array {
        return array(
            "Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi", "Dimanche"
        );
    }

}
