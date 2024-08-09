<?php

namespace App\Enums;

enum TypeInformationRequired: string
{
    case NOMBRE = 'nombre';
    case APELLIDO = 'apellido';
    case NOMBRE_COMPLETO = 'nombre completo';
    case TELEFONO = 'telefono';
    case CORREO = 'correo';
    case EDAD = 'edad';
    case FECHA_NACIMIENTO = 'fecha nacimiento';
    case DIRECCION = 'direccion';
    case CIUDAD = 'ciudad';
    case PAIS = 'pais';
    case CODIGO_POSTAL = 'codigo postal';
    case NUMERO_DE_DOCUMENTO = 'numero de documento';
    case PASAPORTE = 'pasaporte';
    case PROFESION = 'profesion';
    case EMPRESA = 'empresa';
    case SITIO_WEB = 'sitio web';
    case REDES_SOCIALES = 'redes sociales';
    case GENERO = 'genero';
    case IDIOMA = 'idioma';
    case NUMERO_TARJETA = 'numero tarjeta';

    public static function getValues(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function getRegexPattern(): string
    {
        return match($this) {
            self::NOMBRE => '/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]{2,100}$/',
            self::APELLIDO => '/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]{2,100}$/',
            self::NOMBRE_COMPLETO => '/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]{2,100}$/',
            self::TELEFONO => '/^\+?[0-9]{8,15}$/',
            self::CORREO => '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,100}$/',
            self::EDAD => '/^[0-9]{1,3}$/',
            self::FECHA_NACIMIENTO => '/^(0[1-9]|[12][0-9]|3[01])[- /.](0[1-9]|1[012])[- /.](19|20)\d\d$/',
            self::DIRECCION => '/^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\s,.-]{5,100}$/',
            self::CIUDAD => '/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s-]{2,100}$/',
            self::PAIS => '/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]{2,100}$/',
            self::CODIGO_POSTAL => '/^[0-9]{4,10}$/',
            self::NUMERO_DE_DOCUMENTO => '/^[0-9]{8}[a-zA-Z]$/',
            self::PASAPORTE => '/^[a-zA-Z0-9]{6,12}$/',
            self::PROFESION => '/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]{2,100}$/',
            self::EMPRESA => '/^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\s&.-]{2,100}$/',
            self::SITIO_WEB => '/^(http:\/\/www\.|https:\/\/www\.|http:\/\/|https:\/\/)?[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(:[0-9]{1,5})?(\/.*)?$/',
            self::REDES_SOCIALES => '/^@?[a-zA-Z0-9_]{1,100}$/',
            self::GENERO => '/^(masculino|femenino|otro)$/i',
            self::IDIOMA => '/^[a-zA-ZáéíóúÁÉÍÓÚñÑ]{2,20}$/',
            self::NUMERO_TARJETA => '/^[0-9]{13,19}$/',
        };
    }

    public function getErrorMessage(): string
    {
        return match($this) {
            self::NOMBRE => 'El nombre debe contener solo letras y espacios, y tener entre 2 y 100 caracteres.',
            self::APELLIDO => 'El apellido debe contener solo letras y espacios, y tener entre 2 y 100 caracteres.',
            self::NOMBRE_COMPLETO => 'El nombre completo debe contener solo letras y espacios, y tener entre 2 y 100 caracteres.',
            self::TELEFONO => 'El número de teléfono debe contener entre 8 y 15 dígitos, con un prefijo opcional "+" al inicio.',
            self::CORREO => 'La dirección de correo electrónico debe seguir el formato estándar, como "ejemplo@dominio.com".',
            self::EDAD => 'La edad debe ser un número entre 1 y 999.',
            self::FECHA_NACIMIENTO => 'La fecha de nacimiento debe estar en el formato DD/MM/YYYY.',
            self::DIRECCION => 'La dirección debe tener entre 5 y 100 caracteres y puede incluir letras, números y símbolos como ",", ".", "-".',
            self::CIUDAD => 'La ciudad debe contener solo letras y espacios, y tener entre 2 y 100 caracteres.',
            self::PAIS => 'El país debe contener solo letras y espacios, y tener entre 2 y 100 caracteres.',
            self::CODIGO_POSTAL => 'El código postal debe contener entre 4 y 10 dígitos.',
            self::NUMERO_DE_DOCUMENTO => 'El número de documento debe tener 8 dígitos seguidos por una letra.',
            self::PASAPORTE => 'El número de pasaporte debe tener entre 6 y 12 caracteres alfanuméricos.',
            self::PROFESION => 'La profesión debe contener solo letras y espacios, y tener entre 2 y 100 caracteres.',
            self::EMPRESA => 'El nombre de la empresa debe tener entre 2 y 100 caracteres y puede incluir letras, números y símbolos como "&", ".", "-".',
            self::SITIO_WEB => 'La URL debe ser válida, comenzando con "http://" o "https://", y seguir el formato "www.dominio.com".',
            self::REDES_SOCIALES => 'El nombre de usuario de la red social debe contener solo letras, números o guiones bajos y tener entre 1 y 100 caracteres.',
            self::GENERO => 'El género debe ser "masculino", "femenino" u "otro".',
            self::IDIOMA => 'El idioma debe contener solo letras y tener entre 2 y 20 caracteres.',
            self::NUMERO_TARJETA => 'El número de tarjeta debe contener entre 13 y 19 dígitos.',
        };
    }
}
