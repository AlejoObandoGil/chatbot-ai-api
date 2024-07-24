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
            self::NOMBRE => 'Por favor, ingrese un nombre válido.',
            self::APELLIDO => 'Por favor, ingrese un apellido válido.',
            self::NOMBRE_COMPLETO => 'Por favor, ingrese un nombre completo válido.',
            self::TELEFONO => 'Por favor, ingrese un número de teléfono válido.',
            self::CORREO => 'Por favor, ingrese una dirección de correo electrónico válida.',
            self::EDAD => 'Por favor, ingrese una edad válida.',
            self::FECHA_NACIMIENTO => 'Por favor, ingrese una fecha de nacimiento válida en formato DD/MM/YYYY.',
            self::DIRECCION => 'Por favor, ingrese una dirección válida.',
            self::CIUDAD => 'Por favor, ingrese una ciudad válida.',
            self::PAIS => 'Por favor, ingrese un país válido.',
            self::CODIGO_POSTAL => 'Por favor, ingrese un código postal válido.',
            self::NUMERO_DE_DOCUMENTO => 'Por favor, ingrese un nuemro de documento válido.',
            self::PASAPORTE => 'Por favor, ingrese un número de pasaporte válido.',
            self::PROFESION => 'Por favor, ingrese una profesión válida.',
            self::EMPRESA => 'Por favor, ingrese un nombre de empresa válido.',
            self::SITIO_WEB => 'Por favor, ingrese una URL válida.',
            self::REDES_SOCIALES => 'Por favor, ingrese un nombre de usuario de red social válido.',
            self::GENERO => 'Por favor, ingrese "masculino", "femenino" u "otro".',
            self::IDIOMA => 'Por favor, ingrese un idioma válido.',
            self::NUMERO_TARJETA => 'Por favor, ingrese un número de tarjeta válido.',
        };
    }
}
