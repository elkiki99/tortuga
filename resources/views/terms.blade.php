<x-layouts.app :title="__('Términos y Condiciones • Tortuga')">
    <div class="container mx-auto mt-6 mb-12 mx-4 md:px-6 lg:px-8">
        <div class="space-y-6">
            <div>
                <flux:heading size="xl" level="1">
                    Términos y Condiciones
                </flux:heading>

                <flux:subheading level="1" size="lg" class="mb-6">
                    Bienvenido/a a {{ config('app.name') }}. Al acceder a nuestro sitio web o utilizar nuestros
                    servicios, aceptás los presentes Términos y Condiciones. Te recomendamos leerlos detenidamente antes
                    de navegar o realizar una compra.
                </flux:subheading>
            </div>

            <flux:separator variant="subtle" />

            <div class="space-y-2">
                <flux:subheading size="lg" level="2">
                    1. Uso del sitio
                </flux:subheading>
                <flux:text>
                    Este sitio está destinado al uso personal y no comercial de los usuarios. Al utilizarlo, te
                    comprometés a no realizar actividades que puedan afectar el correcto funcionamiento del mismo o que
                    infrinjan la legislación vigente en Uruguay.
                </flux:text>
            </div>

            <div class="space-y-2">
                <flux:subheading size="lg" level="2">
                    2. Productos de segunda mano
                </flux:subheading>
                <flux:text>
                    Todos los productos que ofrecemos son de segunda mano, seleccionados cuidadosamente para garantizar
                    su calidad y estado. Al realizar una compra, entendés y aceptás que se trata de artículos
                    reutilizados, que pueden presentar leves signos de uso.
                </flux:text>
            </div>

            <div class="space-y-2">
                <flux:subheading size="lg" level="2">
                    3. Compras y pagos
                </flux:subheading>
                <flux:text>
                    Las compras se realizan a través de nuestra plataforma online. Aceptamos los métodos de pago
                    especificados al momento del checkout. Nos reservamos el derecho de rechazar o cancelar pedidos por
                    motivos como falta de stock, errores en el precio o sospechas de fraude.
                </flux:text>
            </div>

            <div class="space-y-2">
                <flux:subheading size="lg" level="2">
                    4. Entrega y devoluciones
                </flux:subheading>
                <flux:text>
                    Una vez realizado el pago a través de nuestro sitio web, nos pondremos en contacto contigo para
                    coordinar la entrega del producto en uno de nuestros puntos de encuentro disponibles. Actualmente no
                    realizamos envíos.
                </flux:text>
            </div>

            <div class="space-y-2">
                <flux:subheading size="lg" level="2">
                    5. Propiedad intelectual
                </flux:subheading>
                <flux:text>
                    Todo el contenido publicado en este sitio, incluyendo textos, imágenes, logotipos y diseños, es
                    propiedad de {{ config('app.name') }} o de terceros que han autorizado su uso. No está permitido
                    copiar, reproducir o distribuir dicho contenido sin autorización previa por escrito.
                </flux:text>
            </div>

            <div class="space-y-2">
                <flux:subheading size="lg" level="2">
                    6. Responsabilidad
                </flux:subheading>
                <flux:text>
                    No nos hacemos responsables por daños directos o indirectos derivados del uso de nuestro sitio o
                    productos, incluyendo pero no limitado a errores en la información, interrupciones del servicio o
                    virus informáticos.
                </flux:text>
            </div>

            <div class="space-y-2">
                <flux:subheading size="lg" level="2">
                    7. Modificaciones a los términos
                </flux:subheading>
                <flux:text>
                    Podemos actualizar estos Términos y Condiciones en cualquier momento. Los cambios entrarán en
                    vigencia una vez publicados en esta misma página. Es tu responsabilidad revisarlos periódicamente.
                </flux:text>
            </div>

            <div class="space-y-2">
                <flux:subheading size="lg" level="2">
                    8. Contacto
                </flux:subheading>
                <flux:text>
                    Si tenés dudas o comentarios sobre estos Términos y Condiciones, podés escribirnos a:
                    {{ config('mail.from.address') }}.
                </flux:text>
            </div>

            <flux:separator variant="subtle" />

            <flux:text>
                Última actualización: Agosto 2025
            </flux:text>
        </div>
    </div>
</x-layouts.app>
