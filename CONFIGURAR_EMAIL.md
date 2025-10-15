# Configuración de Email con Gmail

Para poder enviar emails reales desde tu aplicación local usando Gmail, sigue estos pasos:

## Paso 1: Habilitar verificación en 2 pasos en Gmail

1. Ve a tu cuenta de Google: https://myaccount.google.com/
2. Ve a "Seguridad"
3. Habilita "Verificación en 2 pasos"

## Paso 2: Crear una contraseña de aplicación

1. En la misma página de Seguridad, ve a "Contraseñas de aplicaciones"
2. Selecciona "Correo" y "Windows Computer" (o el que prefieras)
3. Haz clic en "Generar"
4. **Copia la contraseña de 16 caracteres** que te muestra (algo como: `abcd efgh ijkl mnop`)

## Paso 3: Configurar el archivo EmailSender.php

Abre el archivo: `app/helpers/EmailSender.php`

Busca estas líneas (alrededor de la línea 14-16):

```php
'username' => '', // CAMBIAR: Tu email de Gmail
'password' => '', // CAMBIAR: Tu contraseña de aplicación de Gmail
'from_email' => '', // CAMBIAR: Tu email de Gmail
```

Y reemplázalas con tus datos:

```php
'username' => 'tuemail@gmail.com',
'password' => 'abcd efgh ijkl mnop', // La contraseña de aplicación de 16 caracteres
'from_email' => 'tuemail@gmail.com',
```

## Paso 4: Probar el envío

1. Recarga la aplicación
2. Ve a una cotización
3. Haz clic en "Email"
4. Ingresa el email de destino
5. Haz clic en "Enviar Email"

¡Listo! El email debería enviarse correctamente.

## Solución de problemas

### Error: "SMTP connect() failed"
- Verifica que hayas habilitado la verificación en 2 pasos
- Asegúrate de estar usando la contraseña de aplicación, NO tu contraseña normal de Gmail

### Error: "Invalid address"
- Verifica que el email de destino sea válido
- Verifica que hayas configurado correctamente el `from_email`

### El email llega a spam
- Es normal en desarrollo. En producción usa un dominio verificado y SPF/DKIM

## Alternativas a Gmail

Si no quieres usar Gmail, puedes usar:

- **Mailtrap.io** (para pruebas, no envía emails reales)
- **SendGrid** (gratis hasta 100 emails/día)
- **Mailgun** (gratis hasta 5,000 emails/mes)
- **Amazon SES** (muy económico)

Para cambiar el proveedor, solo modifica la configuración SMTP en `EmailSender.php`.
