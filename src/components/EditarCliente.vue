<template>
    <!-- Botón para abrir el modal -->
    <label :for="nombre" class="btn btn-outline btn-warning">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
            class="size-6">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
        </svg>
    </label>

    <!-- Modal -->
    <input type="checkbox" :id="nombre" class="modal-toggle" />
    <div class="modal" role="dialog">
        <div class="modal-box">

            <h3 class="text-lg font-bold mb-2 text-center">Editar cliente</h3>

            <!-- Nombre cliente -->
            <div class="label">
                <span class="label-text font-bold">Nombre</span>
            </div>
            <input v-model="localNombre" type="text" placeholder="Ingresa el nombre del cliente"
                class="input input-bordered w-full max-w-xs mb-2" />

            <!-- Comuna -->
            <div class="label">
                <span class="label-text font-bold">Comuna</span>
            </div>
            <select v-model="ciudadSeleccionada" class="select select-bordered w-full max-w-xs">
                <option disabled selected> {{ comuna }} </option>
                <option v-for="ciudad in ciudades" :key="ciudad" :value="ciudad">{{ ciudad }}</option>
            </select>

            <!-- Sector -->
            <div class="label">
                <span class="label-text font-bold">Sector</span>
            </div>
            <select v-model="sectorSeleccionado" class="select select-bordered w-full max-w-xs">
                <option disabled selected> {{ sector }} </option>
                <option v-for="sector in sectoresFiltrados" :key="sector.ID" :value="sector.ID">
                    {{ sector.NombreSector }}</option>
            </select>

            <!-- Dia de reparto -->
            <p class="my-2 mx-1 font-bold">Día de reparto actual: {{ dia }}</p>
            <p class="my-2 mx-1 font-bold text-red-500" v-if="diaReparto">Nuevo día de reparto: {{ diaReparto }}</p>

            <div class="label">
                <span class="label-text font-bold">Día de reparto excepcional</span>
            </div>
            <select v-model="diareparto" class="select select-bordered w-full max-w-xs">
                <option v-for="dia in diasdelasemana">
                    {{ dia }}
                </option>
            </select>

            <!-- Dirección -->
            <div class="label">
                <span class="label-text font-bold">Dirección</span>
            </div>
            <input v-model="localDireccion" type="text" placeholder="Ingresa la dirección del cliente"
                class="input input-bordered w-full max-w-xs mb-2">

            <!-- Teléfono -->
            <div class="label">
                <span class="label-text font-bold">Teléfono</span>
            </div>
            <input v-model="localTelefono" type="text" placeholder="Ej. 56922446688"
                class="input input-bordered w-full max-w-xs mb-2">

            <!-- Teléfono 2 -->
            <div class="label">
                <span class="label-text font-bold">Teléfono 2</span>
            </div>
            <input v-model="localTelefono2" type="text" placeholder="Ej. 56922446688"
                class="input input-bordered w-full max-w-xs mb-2">

            <!-- Link de Google Maps -->
            <div class="label">
                <span class="label-text font-bold">Link de Google Maps</span>
            </div>
            <input v-model="localLinkMaps" type="text" placeholder="https://maps.app.goo.gl/..."
                class="input input-bordered w-full max-w-xs mb-2">

            <!-- Observaciones -->
            <div class="label">
                <span class="label-text font-bold">Observaciones</span>
            </div>
            <input v-model="localObservaciones" type="text" placeholder="Casa esquina. Árbol afuera."
                class="input input-bordered w-full max-w-xs mb-2">

            <!-- Botones del modal -->
            <div class="modal-action">
                <label :for="nombre" class="btn">Salir</label>
                <button class="btn btn-outline btn-warning" @click="editarCliente(id)">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                    </svg>
                    Editar cliente
                </button>
            </div>
        </div>
    </div>
</template>

<script>
//Para usar axios, primero hay que instalarlo usando: 'npm install axios'
import axios from "axios";

export default {
    //Nombre del componente
    name: "EditarCliente",

    props: {
        id: Number,
        nombre: String,
        comuna: String,
        sector: String,
        id_sector: Number,
        dia: String,
        direccion: String,
        telefono: String,
        telefono2: String,
        linkmaps: String,
        observaciones: String,
    },

    data() {
        return {
            //Array para guardar datos de la API
            sectores: [],
            productos: [],
            localNombre: this.nombre,
            diareparto: '', //dre
            localDireccion: this.direccion,
            localTelefono: this.telefono,
            localTelefono2: this.telefono2,
            localLinkMaps: this.linkmaps,
            localObservaciones: this.observaciones,
            diasdelasemana: ['Lunes', 'Martes', 'Miércoles', 'Jueves', "Viernes", "Sábado", "Domingo"],
            ciudadSeleccionada: this.comuna,
            sectorSeleccionado: this.sector,
        };
    },

    methods: {
        editarCliente(id) {
            let n = this.localNombre;
            let d = this.localDireccion;
            let ids = '';
            let t = this.localTelefono;
            let t2 = this.localTelefono2;
            let l = this.localLinkMaps;
            let o = this.localObservaciones;
            let dre = this.diareparto;

            /** Este If compara el sector PROP con el sector DATA
             * Como el valor DATA llega por PROP, si este valor no cambia
             * entonces se usa el mismo id, que también viene por prop
             * si el valor cambia, el id se actualiza solo, en la variable SECTORSELECCIONADO
             */
            if (this.sector === this.sectorSeleccionado) {
                ids = this.id_sector
            } else {
                ids = this.sectorSeleccionado;
            }

            let url = `https://nuestrocampo.cl/demo/clientes/update.php?id=${id}&nombre=${n}&direccion=${d}&idsector=${ids}&telefono=${t}&telefono2=${t2}&linkmaps=${l}&observacion=${o}`;
            if (dre !== "") {
              url += `&dre=${dre}`;
            }
            console.log("Actualizando cliente...");
            axios.put(url);
            location.reload();
        },
    },

    computed: {
        ciudades() {
            // Obtener una lista única de comunas (ciudades)
            const ciudadesUnicas = new Set(this.sectores.map(sector => sector.Comuna));
            return Array.from(ciudadesUnicas);
        },
        sectoresFiltrados() {
            return this.sectores.filter(sector => sector.Comuna === this.ciudadSeleccionada);
        },
        diaReparto() {
            const sector = this.sectores.find(sector => sector.ID === this.sectorSeleccionado);
            return sector ? sector.DiaReparto : null;
        }
    },

    created() {
        //Leer sectores
        let url = "https://nuestrocampo.cl/demo/sectores/read.php";

        axios.get(url).then((response) => (this.sectores = response.data));

        let url2 = "https://nuestrocampo.cl/demo/productos/read.php";

        axios.get(url2).then((response) => (this.productos = response.data));
    },
};
</script>