<template>
    <!-- Botón CREAR NUEVO CLIENTE -->
    <button class="btn btn-outline btn-success mr-2 mb-4" onclick="crearClienteModal.showModal()">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
            class="size-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
        </svg>

        Crear nuevo cliente
    </button>

    <!-- Modal CREAR NUEVO CLIENTE -->
    <dialog id="crearClienteModal" class="modal">
        <div class="modal-box">
            <h3 class="text-lg font-bold mb-2 text-center">Nuevo cliente</h3>

            <!-- Nombre del cliente -->
            <div class="label">
                <span class="label-text font-bold">Nombre</span>
            </div>
            <input v-model="nombre" type="text" placeholder="Ingresa el nombre del cliente"
                class="input input-bordered w-full max-w-xs mb-2" />

            <!-- Comuna -->
            <div class="label">
                <span class="label-text font-bold">Comuna</span>
            </div>
            <select v-model="ciudadSeleccionada" class="select select-bordered w-full max-w-xs">
                <option v-for="ciudad in ciudades" :key="ciudad" :value="ciudad">{{ ciudad }}</option>
            </select>

            <!-- Sector -->
            <div class="label">
                <span class="label-text font-bold">Sector</span>
            </div>
            <select v-model="sectorSeleccionado" class="select select-bordered w-full max-w-xs">
                <option v-for="sector in sectoresFiltrados" :key="sector.ID" :value="sector.ID">{{
                sector.NombreSector }}</option>
            </select>
            <p v-if="diaReparto">Día de reparto sugerido: {{ diaReparto }}</p>

            <!-- Día de reparto -->
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
            <input v-model="direccion" type="text" placeholder="Ingresa la dirección del cliente"
                class="input input-bordered w-full max-w-xs mb-2">

            <!-- Teléfono -->
            <div class="label">
                <span class="label-text font-bold">Teléfono</span>
            </div>
            <input v-model="telefono" type="text" placeholder="Ej. 56922446688"
                class="input input-bordered w-full max-w-xs mb-2">

            <!-- Teléfono 2 -->
            <div class="label">
                <span class="label-text font-bold">Teléfono 2</span>
            </div>
            <input v-model="telefono2" type="text" placeholder="Ej. 56922446688"
                class="input input-bordered w-full max-w-xs mb-2">

            <!-- Link de Google Maps -->
            <div class="label">
                <span class="label-text font-bold">Link de Google Maps</span>
            </div>
            <input v-model="linkmaps" type="text" placeholder="https://maps.app.goo.gl/..."
                class="input input-bordered w-full max-w-xs mb-2">

            <!-- Observaciones -->
            <div class="label">
                <span class="label-text font-bold">Observaciones</span>
            </div>
            <input v-model="observaciones" type="text" placeholder="Casa esquina. Árbol afuera."
                class="input input-bordered w-full max-w-xs mb-2">

            <!-- Acciones del modal -->
            <div class="modal-action">
                <form method="dialog">
                    <button class="btn btn-outline btn-success mr-2" @click="crearCliente()">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        Añadir cliente</button>
                    <button class="btn btn-outline btn-error">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                        Salir</button>
                </form>
            </div>
        </div>
    </dialog>
</template>

<script>
import axios from "axios";

export default {
    //Nombre del componente
    name: "CrearClienteModal",

    data() {
        return {
            //Array para guardar datos de la API
            sectores: [],
            productos: [],
            nombre: "", //Nombre del cliente
            nombresector: "",
            comuna: "",
            direccion: "",
            telefono: "",
            telefono2: "",
            linkmaps: "",
            diareparto: '', //dre
            observaciones: '',
            diasdelasemana: ['Lunes', 'Martes', 'Miércoles', 'Jueves', "Viernes", "Sábado", "Domingo"],
            ciudadSeleccionada: null,
            sectorSeleccionado: null, //ID del sector
            productoSeleccionado: 1,
        };
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
        },
    },

    methods: {
        crearCliente() {
            let n = this.nombre;
            let d = this.direccion;
            let ids = this.sectorSeleccionado;
            let t = this.telefono;
            let t2 = this.telefono2;
            let l = this.linkmaps;
            let o = this.observaciones;
            let dre = this.diareparto;

            let url = `https://nuestrocampo.cl/demo/clientes/create.php?nombre=${n}&direccion=${d}&idsector=${ids}&telefono=${t}&telefono2=${t2}&linkmaps=${l}&observacion=${o}`;
            if (dre !== "") {
              // Si diareparto no está vacío, ejecuta el código aquí  
              url += `&dre=${dre}`;
            }
            axios.post(url);
            location.reload();
        },
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