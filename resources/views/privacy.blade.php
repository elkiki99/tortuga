<x-layouts.app :title="__('Privacidad • Tortuga')">
    <div class="container mx-auto mt-6 mb-12 mx-4 md:px-6 lg:px-8">
        <div class="space-y-6">
            <div>
                <flux:heading size="xl" level="1">
                    Política de Privacidad
                </flux:heading>

                <flux:subheading level="1" size="lg" class="mb-6">
                    En {{ config('app.name') }}, tu privacidad es una prioridad. Esta política detalla cómo recopilamos, utilizamos y protegemos tu información personal cuando interactuás con nuestro sitio web o nuestros servicios.
                </flux:subheading>
            </div>

            <flux:separator variant="subtle" />

            <div class="space-y-2">
                <flux:subheading size="lg" level="2">
                    ¿Qué información recopilamos?
                </flux:subheading>
                <flux:text>
                    Podemos recopilar información personal como tu nombre, dirección de correo electrónico, número de teléfono, dirección de envío y detalles de pago cuando creás una cuenta, hacés un pedido o te comunicás con nosotros.
                </flux:text>
            </div>

            <div class="space-y-2">
                <flux:subheading size="lg" level="2">
                    ¿Cómo utilizamos tu información?
                </flux:subheading>
                <flux:text>
                    Utilizamos tus datos para procesar tus compras, gestionar envíos, responder tus consultas, mejorar nuestros productos y servicios, y enviarte información relevante sobre novedades, descuentos o cambios importantes si así lo permitís.
                </flux:text>
            </div>

            <div class="space-y-2">
                <flux:subheading size="lg" level="2">
                    Protección de datos
                </flux:subheading>
                <flux:text>
                    Implementamos medidas de seguridad técnicas y organizativas para proteger tu información contra accesos no autorizados, alteraciones o destrucción. No compartimos tu información con terceros, salvo cuando sea necesario para completar una transacción o cumplir con obligaciones legales.
                </flux:text>
            </div>

            <div class="space-y-2">
                <flux:subheading size="lg" level="2">
                    Cookies y tecnologías similares
                </flux:subheading>
                <flux:text>
                    Nuestro sitio puede utilizar cookies para mejorar tu experiencia de navegación, recordar tus preferencias y analizar el uso del sitio. Podés configurar tu navegador para rechazar cookies si preferís.
                </flux:text>
            </div>

            <div class="space-y-2">
                <flux:subheading size="lg" level="2">
                    Tus derechos y control sobre tu información
                </flux:subheading>
                <flux:text>
                    Tenés derecho a acceder, corregir, actualizar o eliminar tus datos personales en cualquier momento. Si querés ejercer alguno de estos derechos, escribinos a nuestro correo de contacto.
                </flux:text>
            </div>

            <div class="space-y-2">
                <flux:subheading size="lg" level="2">
                    Menores de edad
                </flux:subheading>
                <flux:text>
                    Nuestros servicios no están dirigidos a menores de 18 años. No recopilamos intencionalmente información de menores sin consentimiento verificable de sus padres o tutores.
                </flux:text>
            </div>

            <div class="space-y-2">
                <flux:subheading size="lg" level="2">
                    Modificaciones a esta política
                </flux:subheading>
                <flux:text>
                    Nos reservamos el derecho de actualizar esta política en cualquier momento. Cualquier cambio será publicado en esta página, con la fecha de la última actualización al final del documento.
                </flux:text>
            </div>

            <div class="space-y-2">
                <flux:subheading size="lg" level="2">
                    Contacto
                </flux:subheading>
                <flux:text>
                    Si tenés preguntas o necesitás más información sobre nuestra política de privacidad, podés escribirnos a: {{ config('mail.from.address') }}.
                </flux:text>
            </div>

            <flux:separator variant="subtle" />

            <flux:text>
                Última actualización: Agosto 2025
            </flux:text>
        </div>
    </div>
</x-layouts.app>
