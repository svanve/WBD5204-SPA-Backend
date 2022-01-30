<?php
namespace App\Models;

use Exception;

class FormValidation {
    private $db;
    private $inputData;
    private $rules;
    private $errors = [];
    private $customMessages = [];
    private $germanField;

    public function __construct(Database $db, array $inputData)
    {
        $this->db = $db;
        $this->inputData = $inputData;
    }

    public function setRules(array $rules)
    {
        $this->rules = $rules;
    }

    public function setMessages($customMessages)
    {
        $this->customMessages = $customMessages;
    }

    public function validate()
    {
        foreach ($this->rules as $field => $fieldRules) {
            $fieldRules = explode('|', $fieldRules);

            if (!in_array('required', $fieldRules)) {
                if (empty($this->inputData[$field])) {
                    continue;
                }
            }

            $this->validateField($field, $fieldRules);
        }
    }

    private function validateField(string $field, array $fieldRules)
    {
        usort($fieldRules, function($firstRule, $secondRule) {
            if($firstRule === 'required') {
                return -1;
            }

            return 1;
        });

        foreach($fieldRules as $fieldRule) {
            $ruleSegments = explode(':', $fieldRule);

            $fieldRule = $ruleSegments[0];

            if(isset($ruleSegments[1])) {
                $satisfier = $ruleSegments[1];
            } else {
                $satisfier = null;
            }

            try {
                $this->{$fieldRule}($field, $satisfier);
            } catch (Exception $e) {
                if (isset($this->customMessages["$field.$fieldRule"])) {
                    $this->errors[$field][] = $this->customMessages["$field.$fieldRule"];
                } else {
                    $this->errors[$field][] = $e->getMessage();
                }
                if ($fieldRule === 'required') {
                    break;
                }
            }
        }
    }

    public function fails() 
    {
        return count($this->errors) ? true : false;
    }

    public function getErrors() 
    {
        return $this->errors;
    }

    private function required(string $field)
    {
        if (!isset($this->inputData[$field]) || empty($this->inputData[$field])) {
            $this->parseFieldName($field);
            throw new Exception("Das Feld {$this->germanField} ist ein Pflichtfeld.");
        }
    }

    private function alnum(string $field) 
    {
        if (!ctype_alnum($this->inputData[$field])) {
            $this->parseFieldName($field);
            throw new Exception("Das Feld {$this->germanField} darf nur Buchstaben und Ziffern enthalten.");
        }
    }

    private function min(string $field, string $satisfier) 
    {
        if (strlen($this->inputData[$field]) < (int) $satisfier) {
            $this->parseFieldName($field);
            throw new Exception("Das Feld {$this->germanField} muss mindestens $satisfier Zeichen lang sein.");
        }
    }

    private function max(string $field, string $satisfier) 
    {
        if (strlen($this->inputData[$field]) > $satisfier) {
            $this->parseFieldName($field);
            throw new Exception("Das Feld {$this->germanField} darf höchstens $satisfier Zeichen lang sein.");
        }
    }

    private function email(string $field) 
    {
        if (!filter_var($this->inputData[$field], FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Bitte gib eine gültige Mailadresse ein.");
        }
    }

    private function matches(string $field, string $satisfier) 
    {
        if ($this->inputData[$field] !== $this->inputData[$satisfier]) {
            throw new Exception("Sieht aus, als hättest du das Passwort nicht richtig wiederholt.");
        }
    }

    private function available(string $field, string $satisfier) {
        $query = $this->db->table($satisfier)->where($field, '=', $this->inputData[$field]);

        $this->parseFieldName($field);
        if ($query->count()) {
            throw new Exception("{$this->germanField} ist schon vergeben.");
        }
    }

    private function exists(string $field, string $satisfier) {
        $query = $this->db->table($satisfier)->where($field, '=', $this->inputData[$field]);
    }

    private function parseFieldName($field) 
    {
        if($field === 'email') {
            $this->germanField = 'Mailadresse';
        } elseif ($field === 'password') {
            $this->germanField = 'Passwort';
        } elseif ($field === 'passwordAgain') {
            $this->germanField = 'Passwort wiederholen';
        } elseif ($field === 'alias') {
            $this->germanField = 'Username';
        } elseif ($field === 'firstName') {
            $this->germanField = 'Vorname';
        } elseif ($field === 'lastName') {
            $this->germanField = 'Nachname';
        } elseif ($field === 'title') {
            $this->germanField = 'Titel';
        } elseif ($field === 'body') {
            $this->germanField = 'Inhalt';
        } elseif (strpos($field, 'step')) {
            $this->germanField = 'Zwischenschritt';
        } elseif ($field === 'deadline') {
            $this->germanField = 'Deadline';
        } elseif ($field === 'assigned') {
            $this->germanField = 'Zuständig';
        } elseif ($field === 'name') {
            $this->germanField = 'WG-Name';
        } elseif ($field === 'flats_members') {
            $this->germanField = 'WG-Bewohner';
        }

        return $this->germanField;
    }
}