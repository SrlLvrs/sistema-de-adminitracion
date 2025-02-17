<template>
  <!-- Usar la clase prose para estilos, y max-w-none para usar el ancho completo -->
  <!-- ENCABEZADO -->
  <div class="prose max-w-none">
    <div class="grid grid-cols-1">
      <h1 class="text-center p-4 m-0">Ordenar pedidos</h1>
      <p class="text-center p-4 m-0">Arrastra los pedidos para ordenarlos y presiona en guardar</p>
      <div class="flex justify-center mb-4">
        <label class="btn btn-outline btn-info" @click="guardar()">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
            stroke="currentColor" class="size-6">
            <path stroke-linecap="round" stroke-linejoin="round"
              d="m9 13.5 3 3m0 0 3-3m-3 3v-6m1.06-4.19-2.12-2.12a1.5 1.5 0 0 0-1.061-.44H4.5A2.25 2.25 0 0 0 2.25 6v12a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9a2.25 2.25 0 0 1-2.25-2.25h-5.379a1.5 1.5 0 0 1-1.06-.44Z" />
          </svg>
          Guardar</label>
      </div>
    </div>
  </div>
  <!-- Resultados -->
  <div v-if="pedidosPorFecha.length > 0" class="overflow-x-auto">
    <div v-for="(grupo, index) in pedidosPorFecha" :key="index" class="mb-8">
      <h2 class="text-xl font-bold mb-4">{{ grupo.fecha }}</h2>
      <table class="table table-zebra">
        <thead>
          <tr>
            <th></th>
            <th></th>
            <th>Cliente</th>
            <th>Dirección</th>
            <th>Fecha de Entrega</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(item, itemIndex) in grupo.pedidos" :key="itemIndex" 
              :class="{ 'bg-blue-200': isDragging && draggedIndex === [index, itemIndex], 'opacity-50': isDragging && draggedIndex !== [index, itemIndex] }"
              @drop="drop(index, itemIndex)" 
              @dragover.prevent>
            <td class="drag-handle"
                draggable="true" 
                @dragstart="dragStart(index, itemIndex, $event)"
                @dragend="dragEnd"
                @touchstart="touchStart(index, itemIndex, $event)"
                @touchmove.prevent="touchMove($event)"
                @touchend="touchEnd(index)">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
              </svg>
            </td>
            <td>
              <div v-if="item.IDPA" class="tooltip tooltip-right" data-tip="Pedido creado automáticamente">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                  stroke="currentColor" class="size-6">
                  <path stroke-linecap="round" stroke-linejoin="round"
                    d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 0 2.25-2.25h13.5A2.25 2.25 0 0 0 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5m-9-6h.008v.008H12v-.008ZM12 15h.008v.008H12V15Zm0 2.25h.008v.008H12v-.008ZM9.75 15h.008v.008H9.75V15Zm0 2.25h.008v.008H9.75v-.008ZM7.5 15h.008v.008H7.5V15Zm0 2.25h.008v.008H7.5v-.008Zm6.75-4.5h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V15Zm0 2.25h.008v.008h-.008v-.008Zm2.25-4.5h.008v.008H16.5v-.008Zm0 2.25h.008v.008H16.5V15Z" />
                </svg>
              </div>
            </td>
            <td>{{ item.Nombre }}</td>
            <td>{{ item.Direccion }}, {{ item.NombreSector }}, {{ item.Comuna }}</td>
            <td>{{ item.FechaEntrega }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
  <div v-else class="flex justify-center items-center h-screen">
    <p class="text-center">No hay pedidos asignados a ti, o no eres repartidor. Si crees que es un error, comunícate con
      el administrador.</p>
  </div>
</template>

<script>
//Para usar axios, primero hay que instalarlo usando: 'npm install axios'
import axios from "axios";

export default {
  data() {
    return {
      items: [],
      pedidosPorFecha: [],
      isDragging: false,
      draggedIndex: null,
      ids: [],
      touchStartY: null,
    };
  },
  methods: {
    checkUserSession() {
      const sessionData = JSON.parse(localStorage.getItem('authUser'));
      return sessionData ? sessionData : null;
    },
    dragStart(grupoIndex, itemIndex, event) {
      if (!event.target.closest('.drag-handle')) {
        event.preventDefault();
        return;
      }
      this.isDragging = true;
      this.draggedIndex = [grupoIndex, itemIndex];
      event.dataTransfer.effectAllowed = 'move';
      event.dataTransfer.setData('text/plain', JSON.stringify([grupoIndex, itemIndex]));
    },
    dragEnd() {
      this.isDragging = false;
      this.draggedIndex = null;
    },
    drop(grupoIndex, itemIndex) {
      if (this.draggedIndex !== null) {
        const [draggedGrupoIndex, draggedItemIndex] = this.draggedIndex;
        const draggedItem = this.pedidosPorFecha[draggedGrupoIndex].pedidos.splice(draggedItemIndex, 1)[0];
        this.pedidosPorFecha[grupoIndex].pedidos.splice(itemIndex, 0, draggedItem);
      }
      this.isDragging = false;
      this.draggedIndex = null;
    },
    touchStart(index, itemIndex, event) {
      if (!event.target.closest('.drag-handle')) {
        return;
      }
      this.isDragging = true;
      this.draggedIndex = [index, itemIndex];
      this.touchStartY = event.touches[0].clientY;
    },
    touchMove(event) {
      if (!this.isDragging) return;
      const touchY = event.touches[0].clientY;
      const row = event.target.closest('tr');
      const tableBody = row.parentNode;
      const rowHeight = row.offsetHeight;
      const moveDistance = touchY - this.touchStartY;

      if (Math.abs(moveDistance) > rowHeight / 2) {
        const newIndex = this.draggedIndex[0] + (moveDistance > 0 ? 1 : -1);
        if (newIndex >= 0 && newIndex < this.pedidosPorFecha.length) {
          this.pedidosPorFecha[newIndex].pedidos.splice(this.draggedIndex[1], 0, this.pedidosPorFecha[this.draggedIndex[0]].pedidos.splice(this.draggedIndex[1], 1)[0]);
          this.draggedIndex = [newIndex, this.draggedIndex[1]];
          this.touchStartY = touchY;
        }
      }
    },
    touchEnd(index) {
      this.isDragging = false;
      this.draggedIndex = null;
      this.touchStartY = null;
    },
    guardar() {
      let orden = 0;
      for (const grupo of this.pedidosPorFecha) {
        for (const item of grupo.pedidos) {
          let url = `https://nuestrocampo.cl/demo/pedidos/order.php?orden=${orden}&idp=${item.ID}`;
          axios.put(url);
          orden++;
        }
      }
      location.reload();
    },

    agruparPedidosPorFecha() {
      const grupos = this.items.reduce((acc, pedido) => {
        const fecha = pedido.FechaEntrega.split(' ')[0]; // Obtener solo la fecha
        if (!acc[fecha]) {
          acc[fecha] = [];
        }
        acc[fecha].push(pedido);
        return acc;
      }, {});

      this.pedidosPorFecha = Object.entries(grupos)
        .map(([fecha, pedidos]) => ({ fecha, pedidos }))
        .sort((a, b) => new Date(a.fecha) - new Date(b.fecha));
    },

    formatearFecha(fecha) {
      const opciones = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
      return new Date(fecha).toLocaleDateString('es-ES', opciones);
    },
  },

  mounted() {
    const sessionData = this.checkUserSession();
    if (sessionData) {
      console.log('Sesión iniciada');
      let id_user = sessionData.id;
      if (sessionData.rol === 'Repartidor') {
        let url = `https://nuestrocampo.cl/demo/pedidos/read-week-deliver.php?id=${id_user}`;
        axios.get(url).then((response) => {
          this.items = response.data;
          this.agruparPedidosPorFecha();
        });
      } else {
        console.log('El usuario no es repartidor');
      }
    } else {
      console.log('No hay sesión iniciada');
      this.$router.push({ name: 'LogIn' });
    }
  },
};
</script>

<style scoped>
tr {
  transition: background-color 0.3s, opacity 0.3s;
}

.drag-handle {
  cursor: move;
}

.drag-handle:active {
  cursor: grabbing;
}
</style>
