<template>
    <!-- Modal CREAR NUEVO PEDIDO AUTOMATICO -->
    <!-- Botón para abrir el modal -->
    <label :for="id" class="btn btn-outline btn-success" @click="getDetalle()">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
            class="size-6">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5m-9-6h.008v.008H12v-.008ZM12 15h.008v.008H12V15Zm0 2.25h.008v.008H12v-.008ZM9.75 15h.008v.008H9.75V15Zm0 2.25h.008v.008H9.75v-.008ZM7.5 15h.008v.008H7.5V15Zm0 2.25h.008v.008H7.5v-.008Zm6.75-4.5h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V15Zm0 2.25h.008v.008h-.008v-.008Zm2.25-4.5h.008v.008H16.5v-.008Zm0 2.25h.008v.008H16.5V15Z" />
        </svg>
    </label>

    <!-- Modal -->
    <input type="checkbox" :id="id" class="modal-toggle" />
    <div class="modal" role="dialog">
        <div class="modal-box modal-pedido">
            <h3 class="text-lg font-bold text-center">Nuevo Pedido Automático</h3>

            <!-- Renderización condicional del loading spinner-->
            <div v-if="items.length === 0">
                <span class="loading loading-spinner loading-md"></span>
            </div>

            <!-- Contenido del modal -->
            <div v-else>
                <div v-for="item in items">
                    <!-- Cliente -->
                    <div>
                        <div class="label">
                            <span class="label-text font-bold">Cliente</span>
                        </div>
                        <p> {{ item.Nombre }} - {{ item.Direccion }}, {{ item.NombreSector }}, {{ item.Comuna }}</p>
                    </div>

                    <!-- Día de reparto -->
                    <div>
                        <div class="label">
                            <span class="label-text font-bold">Dia de reparto</span>
                        </div>
                        <p>{{ item.Dia_de_Reparto }}</p>
                    </div>

                    <!-- Frecuencia -->
                    <div>
                        <div class="label">
                            <span class="label-text font-bold">Frecuencia</span>
                        </div>

                        <select v-model="frecuencia_seleccionada" class="select select-bordered w-full max-w-xs">
                            <option v-for="item in frecuencias">
                                {{ item }}
                            </option>
                        </select>
                    </div>

                    <!-- Fecha de entrega -->
                    <div class="mt-2">
                        <div class="label">
                            <span class="label-text font-bold">Fecha de entrega</span>
                        </div>
                        <p class="mb-2">Selecciona la fecha de la primera entrega. Las demás se calcularán en base a
                            ésta.</p>
                        <VCalendar v-model="fecha_reparto_local"></VCalendar>
                    </div>

                    <!-- Observacion -->
                    <div>
                        <div class="label">
                            <span class="label-text font-bold">Observaciones</span>
                        </div>
                        <input v-model="observaciones" type="text" placeholder="3er lunes de cada mes."
                            class="input input-bordered w-full max-w-xs mb-2">
                    </div>

                </div>

                <!-- Todos los productos -->
                <div>
                    <div class="label">
                        <span class="label-text font-bold">Productos</span>
                    </div>

                    <select v-model="producto_seleccionado" class="select select-bordered w-full max-w-xs">
                        <option v-for="(producto, index) in productos" :key="index" :value="index">
                            {{ producto.descripcion }} - ${{ producto.precio }}
                        </option>
                    </select>
                </div>

                <!-- Cantidad -->
                <div>
                    <div class="label">
                        <span class="label-text font-bold">Cantidad</span>
                    </div>
                    <input v-model="cantidad" type="number" placeholder="1"
                        class="input input-bordered w-full max-w-xs" />
                    <label class="btn btn-outline btn-success m-1" @click="agregarProducto()">Añadir</label>
                </div>

                <!-- Productos elegidos -->
                <!-- Sólo aparece si el array productos_elegidos tiene elementos -->
                <div v-if="productos_elegidos.length > 0">
                    <div class="label">
                        <span class="label-text font-bold">Productos elegidos</span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="table">
                            <!-- Cabecera -->
                            <thead>
                                <tr>
                                    <th>Cantidad</th>
                                    <th>Producto</th>
                                    <th>Precio Unitario</th>
                                    <th>Total</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <!-- Cuerpo -->
                            <tbody>
                                <tr v-for="(producto, index) in productos_elegidos" :key="index" :value="index">
                                    <th>{{ producto.cantidad }}</th>
                                    <th>{{ producto.descripcion }}</th>
                                    <td>{{ producto.precio }}</td>
                                    <td>{{ producto.total }}</td>
                                    <button class="btn btn-outline btn-error m-1" @click="borrarProducto(index)">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="size-6">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                        </svg>
                                    </button>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="label">
                        <span class="label-text font-bold">Total: {{ total }}</span>
                    </div>
                </div>

                <!-- Progreso -->
                <div v-if="last_idpa">
                    <div class="label">
                        <span class="label-text font-bold">Creando pedidos...</span>
                    </div>
                    <progress class="progress w-56"></progress>
                </div>

                <!-- Acciones -->
                <div class="modal-action">
                    <!-- Descartar pedido -->
                    <label class="btn" :for="id">Descartar Pedido Automático</label>

                    <!-- Crear pedido -->
                    <button class="btn btn-outline btn-success" @click="GuardarTodo()">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                        </svg>
                        Crear Pedido Automático
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from "axios";
import VCalendar from "../components/VCalendar.vue";

export default {
    //Nombre del componente
    name: "CrearPedidoAutoModal",

    props: {
        id: String,
        cliente: Number,
    },

    data() {
        return {
            items: [],
            productos: [],
            producto_seleccionado: '',
            productos_elegidos: [],
            cantidad: 1,
            total: '',
            fecha_reparto_local: '',
            frecuencias: ['Cada 1 semana', 'Cada 2 semanas', 'Cada 3 semanas', 'Cada 4 semanas'],
            frecuencia_seleccionada: 'Cada 1 semana',
            ultimo_pedido: [],
            last_idpa: '',
            cantidad_pedidos: 0,
            observaciones: '',
        };
    },

    methods: {
        /** Sumar el total */
        adicion() {
            let totalSum = this.productos_elegidos.reduce((acumulador, itemActual) => {
                return acumulador + itemActual.total;
            }, 0);

            this.total = totalSum;
        },
        /** Formatear fecha para mysql */
        formatToMySQLDateTime(dateString) {
            // Crea un objeto Date a partir del Date String
            const date = new Date(dateString);

            // Extrae los componentes de la fecha
            const year = date.getFullYear(); // Año (YYYY)
            const month = String(date.getMonth() + 1).padStart(2, '0'); // Mes (MM)
            const day = String(date.getDate()).padStart(2, '0'); // Día (DD)

            // Extrae los componentes de la hora
            const hours = String(date.getHours()).padStart(2, '0'); // Horas (HH)
            const minutes = String(date.getMinutes()).padStart(2, '0'); // Minutos (MM)

            // Retorna la fecha en formato compatible con MySQL DATETIME
            return `${year}-${month}-${day} ${hours}:${minutes}:00`;
        },
        /** Obtiene info del clientes, todos los productos y asigna la fecha de hoy a Vcalendar al iniciar componente */
        getDetalle() {
            //GET detalle cliente
            let idc = this.cliente
            let url = `https://nuestrocampo.cl/demo/clientes/read-detail.php?id=${idc}`;
            axios.get(url).then((response) => (this.items = response.data));

            //GET todos los productos
            let url2 = "https://nuestrocampo.cl/demo/productos/read.php";
            axios.get(url2).then((response) => (this.productos = response.data));

            this.fecha_reparto_local = new Date();
        },
        /** Copia 1 producto del array de todos los productos, a un array vacío [productos seleccionados] */
        agregarProducto() {
            //Mueve los productos seleccionados del array de todos los productos, a un array vacío
            //los cuales irán finalmente a la base de datos
            if (this.producto_seleccionado !== null) {
                //Nuevo proxy en base al index
                const nuevoArray = this.productos[this.producto_seleccionado];

                //De proxy a objeto
                let spread = { ...nuevoArray }

                //Calcular el total por cantidad de productos
                let total = this.cantidad * spread.precio;

                //Añadir cantidad y total al objeto
                spread['cantidad'] = this.cantidad;
                spread['total'] = total;
                //Añadir pedido
                spread['id_pedido'] = this.items[0].id

                //Añadir el objeto al array (y a la tabla)
                this.productos_elegidos.push(spread);

                //Sumar el total de todos los productos.
                this.adicion();
            }
        },
        /** Elimina los productos de la tabla de productos seleccionados */
        borrarProducto(i) {
            this.productos_elegidos.splice(i, 1);

            //Sumar el total de todos los productos restantes.
            this.adicion();
        },
        /** Calcula la cantidad de pedidos que se crearán en base a cada frecuencia */
        calcularCantidadPedidos(d) {
            if (d === 7) {
                this.cantidad_pedidos = 4
            } else if (d === 14) {
                this.cantidad_pedidos = 2
            } else if (d === 21) {
                this.cantidad_pedidos = 2
            } else if (d === 28) {
                this.cantidad_pedidos = 1
            }
            console.log('los dias son ' + d + ' y la cantidad de pedidos es ' + this.cantidad_pedidos)
        },
        /** Ejecuta las funciones principales */
        async GuardarTodo() {
            //Crea el pedido automático y su detalle
            await this.crearPedido_Auto()

            //Crea el pedido base y su detalle
            await this.crearPedidos()

            //Recargar
            location.reload()
        },
        /** 1. Crea los pedidos automáticos */
        async crearPedido_Auto() {
            //POST pedidos_automáticos
            //1. guardar idcliente, ultimopedido, frecuencia y observacion
            let idc = this.cliente
            let up = this.formatToMySQLDateTime(this.fecha_reparto_local)
            let f = this.frecuencia_seleccionada
            let o = this.observaciones

            let idpa = ''

            let url = `https://nuestrocampo.cl/demo/pedidos/create-auto.php?id_cliente=${idc}&ultimo_pedido=${up}&frecuencia=${f}&observacion=${o}`
            await axios.post(url).then(function (response) {
                console.log(response.data);
                idpa = response.data.id
            });

            //2. guardar idpa, idproducto, cantidad y total
            this.last_idpa = idpa
            this.crearDetallePedido_Auto(idpa)
        },
        /** 2. Crea el detalle de pedidos automáticos */
        async crearDetallePedido_Auto(idpedido) {
            //POST pedidos_auto_productos
            let array = this.productos_elegidos

            for (let i = 0; i < array.length; i++) {
                let idp = array[i].id
                let c = array[i].cantidad
                let t = array[i].total

                let url = `https://nuestrocampo.cl/demo/pedidos/create-auto-detail.php?idpa=${idpedido}&idproducto=${idp}&cantidad=${c}&total=${t}`
                await axios.post(url).then(function (response) {
                    console.log(response.data);
                });
            }
        },
        /** 3. Calcula la cantidad de pedidos y los crea*/
        async crearPedidos() {
            //Obtener fecha, intervalo y cantidad de pedidos
            let f = new Date(this.fecha_reparto_local)
            let dias = this.intervalo
            this.calcularCantidadPedidos(dias);

            //Crear pedidos
            for (let i = 0; i < this.cantidad_pedidos; i++) {
                //Formatea la fecha para mysql
                let fmysql = this.formatToMySQLDateTime(f)
                //Crea un pedido con la fecha actual
                await this.crearTodoslosPedidos(fmysql)
                //Actualiza la fecha del ultimo pedido
                this.actualizarFecha(fmysql)
                //Suma el intervalo a la fecha actual
                f.setDate(f.getDate() + dias);
            }
        },
        /** 4. Crea los pedidos base */
        async crearTodoslosPedidos(fecha) {
            let idc = this.cliente;
            let h = new Date();
            let hsql = this.formatToMySQLDateTime(h)
            let idpa = this.last_idpa;
            let estado = this.observaciones ? 'Pendiente' : 'Agendado';
            let ultimoid = ''

            let url = `https://nuestrocampo.cl/demo/pedidos/create-w-idpa.php?id_cliente=${idc}&hora_creacion=${hsql}&fecha_reparto=${fecha}&idpa=${idpa}&estado=${estado}&observacion=${this.observaciones}`
            await axios.post(url).then(function (response) {
                console.log(response.data);
                ultimoid = response.data.id
            });

            // Crear detalle con el ultimo id
            await this.crearDetalle(ultimoid)
        },
        /** 5. Inserta los productos de la tabla en la base de datos en la tabla intermedia PEDIDOS_PRODUCTOS*/
        async crearDetalle(id_pedido) {
            //Envía todos los productos del array, usando un ciclo for.
            let array = this.productos_elegidos;
            for (let i = 0; i < array.length; i++) {
                //Definir variables
                let p = id_pedido;
                let pro = array[i].id;
                let c = array[i].cantidad;
                let t = array[i].total;

                let url = `https://nuestrocampo.cl/demo/pedidos/create-detail.php?id_pedido=${p}&id_producto=${pro}&cantidad=${c}&total=${t}`
                await axios.post(url).then(function (response) {
                    console.log(response.data)
                })
            }
        },
        /** 6. Obtiene el id del último pedido automático y actualiza el campo UltimoPedido  */
        async actualizarFecha(fecha) {
            //Actualizar por fecha
            let idp = this.last_idpa

            let url = `https://nuestrocampo.cl/demo/pedidos/update-auto-date.php?id=${idp}&fecha=${fecha}`
            await axios.put(url).then(function (response) {
                console.log(response.data);
            });
        },
    },

    components: { VCalendar },

    computed: {
        intervalo() {
            switch (this.frecuencia_seleccionada) {
                case "Cada 1 semana":
                    return 7; // 7 días
                case "Cada 2 semanas":
                    return 14; // 14 días
                case "Cada 3 semanas":
                    return 21; // 21 días
                case "Cada 4 semanas":
                    return 28; // 28 días
                default:
                    return null; // Valor por defecto si no coincide con ningún caso
            }
        }
    },
}
</script>