<template>
    <!-- Usar la clase prose para estilos, y max-w-none para usar el ancho completo -->
    <!-- ENCABEZADO -->
    <div class="prose max-w-none">
        <div class="grid grid-cols-1">
            <h1 class="text-center p-4 m-0">Clientes</h1>
            <div class="flex justify-center">
                <!-- Botón CREAR NUEVO CLIENTE -->
                <CrearCliente />

                <!-- BOTON Exportar a Excel -->
                <Excel :items="filteredItems" />
            </div>
            <div class="flex justify-center">
                <!-- INPUT FILTRAR -->
                <label class="input input-bordered flex items-center gap-2">
                    <input v-model="filterText" type="text" class="grow" placeholder="Nombre o dirección..." />
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor"
                        class="h-4 w-4 opacity-70">
                        <path fill-rule="evenodd"
                            d="M9.965 11.026a5 5 0 1 1 1.06-1.06l2.755 2.754a.75.75 0 1 1-1.06 1.06l-2.755-2.754ZM10.5 7a3.5 3.5 0 1 1-7 0 3.5 3.5 0 0 1 7 0Z"
                            clip-rule="evenodd" />
                    </svg>
                </label>

                <!-- SELECT Filtro de días -->
                <select id="day" v-model="selectedDay" class="select select-bordered max-w-xs ml-2">
                    <option value="">Filtrar por día</option>
                    <option v-for="dia in dias"> {{ dia }} </option>
                </select>

                <!-- SELECT Filtro de Suscripción -->
                <select id="sus" v-model="selectedSus" class="select select-bordered max-w-xs ml-2">
                    <option value="">Filtrar por suscripción</option>
                    <option value="todas">Todas las suscripciones</option>
                    <option v-for="suscripcion in suscripciones" :key="suscripcion" :value="suscripcion">
                        {{ suscripcion }}
                    </option>
                </select>

                <!-- BOTÓN PARA DESACTIVAR EL FILTRO -->
                <button class="btn btn-outline btn-error ml-2" @click="borrarFiltros()">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 9.75 14.25 12m0 0 2.25 2.25M14.25 12l2.25-2.25M14.25 12 12 14.25m-2.58 4.92-6.374-6.375a1.125 1.125 0 0 1 0-1.59L9.42 4.83c.21-.211.497-.33.795-.33H19.5a2.25 2.25 0 0 1 2.25 2.25v10.5a2.25 2.25 0 0 1-2.25 2.25h-9.284c-.298 0-.585-.119-.795-.33Z" />
                    </svg>
                    Borrar todos los filtros</button>
            </div>
        </div>
    </div>
    <!-- RESULTADOS -->
    <div class="overflow-x-auto">
        <table v-if="filteredItems.length > 0" class="table">
            <!-- Head -->
            <thead>
                <tr>
                    <th></th>
                    <th>Nombre</th>
                    <th>Dirección</th>
                    <th>Teléfono</th>
                    <th>Teléfono 2</th>
                    <th>Frecuencia</th>
                    <th>Día de reparto</th>
                    <th>Observaciones</th>
                    <!-- 
                        <th>Preferencias</th>
                    -->
                    <th>Acciones</th>
                </tr>
            </thead>
            <!-- Body -->
            <tbody>
                <!-- GET -->
                <!-- el nombre del array es ITEMS, que debe ser el mismo que se define en DATA() RETURN -->
                <!-- Los resultados deben recorrerse dentro del TR -->
                <tr v-for="item in filteredItems" :key="item.ID">
                    <th>
                        <div v-if="item.Deuda" class="tooltip tooltip-right" data-tip="Cliente con deuda pendiente">
                            <!--
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                            </svg>
                            -->
                            <DetalleClienteDeudor class="reset" :id="item.ID" :label="item.ID + 'deuda'" />
                        </div>
                    </th>
                    <th>
                        <router-link :to="`/clientes/detalle/${item.ID}`">
                            {{ item.Nombre }}
                        </router-link>
                    </th>
                    <td> {{ item.Direccion }}, {{ item.NombreSector }}, {{ item.Comuna }}</td>
                    <td> {{ item.Telefono }}</td>
                    <td> {{ item.Telefono2 }}</td>
                    <td> {{ item.Freq }}</td>
                    <td> {{ item.diaDeReparto }}</td>
                    <td> {{ item.Observacion }}</td>
                    <td>
                        <!-- Crear Pedido -->
                        <CrearPedido :id="item.ID + 'pdd'" :cliente="item.ID" />
                        <!-- Crear Pedido Auto -->
                        <CrearPedidoAuto :id="item.ID + 'auto'" :cliente="item.ID" />
                        <!-- Copiar al portapapeles -->
                        <label class="btn btn-outline mr-2" @click="copyToClipboard(item.ID)">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z" />
                            </svg>
                        </label>
                        <!-- BOTÓN MAPS -->
                        <button class="btn btn-outline btn-info mr-2">
                            <a :href="item.LinkMaps" target="_blank">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="size-6">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                                </svg>
                            </a>
                        </button>

                        <!-- Editar Cliente -->
                        <EditarCliente v-if="this.rol != 'Repartidor'" :id="item.ID" :nombre="item.Nombre"
                            :comuna="item.Comuna" :sector="item.NombreSector" :id_sector="item.IDSector"
                            :dia="item.diaDeReparto" :direccion="item.Direccion" :telefono="item.Telefono"
                            :telefono2="item.Telefono2" :linkmaps="item.LinkMaps" :frecuencia="item.freq"
                            :observaciones="item.Observacion" producto_preferido="no" id_producto_preferido="noid" />

                        <!-- Eliminar Cliente -->
                        <EliminarCliente v-if="this.rol != 'Repartidor'" :id="item.ID + 'eliminar'" />
                    </td>
                </tr>
            </tbody>
        </table>
        <div v-else class="flex justify-center items-center h-screen">
            <p class="text-center">No se encontraron clientes con el filtro de búsqueda actual</p>
        </div>
    </div>
</template>

<style scoped>
.reset {
    all: unset;
}
</style>

<script>
//Para usar axios, primero hay que instalarlo usando: 'npm install axios'
import axios from "axios";
import CrearCliente from "../components/CrearCliente.vue";
import EliminarCliente from "../components/EliminarCliente.vue";
import EditarCliente from "../components/EditarCliente.vue";
import CrearPedido from "../components/CrearPedido.vue";
import CrearPedidoAuto from "../components/CrearPedidoAuto.vue";
import Excel from "../components/Excel.vue";
import DetalleClienteDeudor from "../components/DetalleClienteDeudor.vue";

export default {
    //Nombre del componente
    name: "Clientes",

    data() {
        return {
            //Array para guardar datos de la API
            items: [],
            dias: ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'],
            suscripciones: ['Ninguna', 'Cada 1 semana', 'Cada 2 semanas', 'Cada 3 semanas', 'Cada 4 semanas'],
            filterText: '',
            selectedDay: '',
            selectedSus: '',
            rol: '',
        };
    },

    methods: {
        checkUserSession() {
            const sessionData = JSON.parse(localStorage.getItem('authUser'));
            return sessionData ? sessionData : null;
        },
        async getClientes() {
            let url = "https://nuestrocampo.cl/demo/clientes/read.php";
            await axios.get(url).then((response) => (this.items = response.data));
        },
        borrarFiltros() {
            this.filterText = '';
            this.selectedDay = '';
            this.selectedSus = '';
        },
        async copyToClipboard(info) {
            let link = `https://nuestrocampo.cl/clientes/detalle/${info}`
            if (navigator.clipboard) {
                try {
                    await navigator.clipboard.writeText(link);
                    //alert("Texto copiado al portapapeles usando CLIPBOARD!");
                } catch (err) {
                    console.error("Error al copiar el texto: ", err);
                }
            } else {
                // Fallback para navegadores que no soportan navigator.clipboard
                const textArea = document.createElement("textarea");
                textArea.value = link;
                textArea.style.position = "absolute";
                textArea.style.left = "-9999px";
                document.body.appendChild(textArea);
                textArea.select();
                document.execCommand("copy");
                document.body.removeChild(textArea);
                //alert("Texto copiado al portapapeles usando execCommand!");
            }
        },
    },

    computed: {
        filteredItems() {
            // Normaliza el texto de búsqueda y los campos de los clientes
            const normalizeText = (text) => {
                return text.normalize("NFD").replace(/[\u0300-\u036f]/g, "").toLowerCase();
            };

            // Obtiene el texto de búsqueda normalizado
            const filterTextNormalized = normalizeText(this.filterText);

            // Obtiene el día y la suscripción seleccionados normalizados
            const selectedDayNormalized = normalizeText(this.selectedDay);
            const selectedSusNormalized = normalizeText(this.selectedSus);

            // Filtra los elementos de la lista de clientes según los criterios de filtrado
            return this.items.filter((item) => {
                const matchesText =
                    !filterTextNormalized ||
                    normalizeText(item.Nombre).includes(filterTextNormalized) ||
                    normalizeText(item.Direccion).includes(filterTextNormalized) ||
                    normalizeText(item.NombreSector).includes(filterTextNormalized) ||
                    normalizeText(item.Comuna).includes(filterTextNormalized);

                const matchesDay =
                    !selectedDayNormalized ||
                    normalizeText(item.diaDeReparto) === selectedDayNormalized;

                const matchesSus =
                    !selectedSusNormalized ||
                    (selectedSusNormalized === 'todas' && ['Cada 1 semana', 'Cada 2 semanas', 'Cada 3 semanas', 'Cada 4 semanas'].includes(item.Freq)) ||
                    normalizeText(item.Freq) === selectedSusNormalized;


                // Devuelve los elementos que cumplen con todos los criterios de filtrado
                return matchesText && matchesDay && matchesSus;
            });
        },
    },

    mounted() {
        const sessionData = this.checkUserSession();
        if (sessionData) {
            console.log('Sesión iniciada. Montando...');
            this.rol = sessionData.rol
            this.getClientes()
        } else {
            console.log('No hay sesión iniciada. Redireccionando a login');
            this.$router.push({ name: 'LogIn' });
        }
    },

    components: { CrearCliente, EliminarCliente, EditarCliente, CrearPedido, CrearPedidoAuto, Excel, DetalleClienteDeudor },
}
</script>