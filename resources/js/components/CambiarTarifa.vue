<template>
    <section class="p-4 p-md-5">
        <div class="row d-flex justify-content-center">
            <div class="col-md-10 col-lg-8 col-xl-5">
                <div class="card card-default p-5">
                    <div class="card-body p-4">
                        <div class="text-center mb-4">
                            <h3>Pago</h3>
                        </div>
                        <form action="">
                            <div class="form-floating">
                                <div
                                    v-if="strSuccess"
                                    class="alert alert-success alert-dismissible fade show"
                                    role="alert"
                                >
                                    <button
                                        type="button"
                                        class="btn-close"
                                        data-bs-dismiss="alert"
                                        aria-label="Close"
                                    ></button>
                                    <strong>{{ strSuccess }}</strong>
                                </div>

                                <div
                                    v-if="strError"
                                    class="alert alert-danger alert-dismissible fade show"
                                    role="alert"
                                >
                                    <button
                                        type="button"
                                        class="btn-close"
                                        data-bs-dismiss="alert"
                                        aria-label="Close"
                                    ></button>
                                    <strong>{{ strError }}</strong>
                                </div>
                                <input
                                    id="nombre_completo"
                                    type="text"
                                    class="form-control"
                                    v-model="nombre_completo"
                                    @input="saveToLocalStorage"
                                    required
                                    autofocus
                                    autocomplete="off"
                                    placeholder="Nombre completo"
                                />
                                <label
                                    for="nombre_completo"
                                    class="col-sm-4 col-form-label text-md-right"
                                    >Nombre completo</label
                                >
                            </div>
                            <br />
                            <div class="form-floating">
                                <input
                                    id="numero_tarjeta"
                                    type="text"
                                    class="form-control"
                                    v-model="numero_tarjeta"
                                    required
                                    autofocus
                                    autocomplete="off"
                                    placeholder="Numero de tarjeta"
                                />
                                <label
                                    for="numero_tarjeta"
                                    class="col-sm-4 col-form-label text-md-right"
                                    >Numero de tarjeta</label
                                >
                            </div>
                            <br />
                            <div class="row mb-4">
                                <div class="col-4">
                                    <div class="form-floating">
                                        <input
                                            id="name"
                                            type="text"
                                            class="form-control"
                                            v-model="name"
                                            required
                                            autofocus
                                            autocomplete="off"
                                            placeholder="MM/YY"
                                        />
                                        <label
                                            for="name"
                                            class="col-sm-4 col-form-label text-md-right"
                                            >MM/YY</label
                                        >
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="form-floating">
                                        <input
                                            id="name"
                                            type="text"
                                            class="form-control"
                                            v-model="name"
                                            required
                                            autofocus
                                            autocomplete="off"
                                            placeholder="CVV"
                                        />
                                        <label
                                            for="name"
                                            class="col-sm-4 col-form-label text-md-right"
                                            >CVV</label
                                        >
                                    </div>
                                </div>
                            </div>
                            <br />
                            <div class="text-center">
                                <button
                                    type="submit"
                                    class="button-primary"
                                    @click="cambiarTarifa"
                                >
                                    Pagar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-10 col-lg-8 col-xl-3">
                <div class="card card-default p-5">
                    <div
                        class="card-body p-4"
                        v-for="(tarifa, index) in tarfiasArray"
                        :key="tarifa.id"
                    >
                        <div class="text-center mb-4">
                            <h3>{{ tarifa.tipo_tarifa }}</h3>
                        </div>
                        <div class="col">
                            <p>{{ tarifa.tipo_tarifa }}</p>
                            <b><p>{{ tarifa.precio }}€ / mes</p></b>
                            <p>{{ tarifa.descripcion_tarifa }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script>
export default {
    name: "CambiarTarifa",
    data() {
        return {
            nombre_completo: "",
            numero_tarjeta: "",
            tarifas: null,
            tarfiasArray: [],
            factura: null,
            membership: null,
            idtarifa: this.$route.params.idTarifa,
            iduser: this.$route.params.idUser,
            isLoggedin: false,
        };
    },
    created() {
        if (!window.Laravel.isLoggedin) {
            this.isLoggedin = true;
        }
    },
    mounted() {
        const dataFromLocalStorage = localStorage.getItem("dataPago");
        if (dataFromLocalStorage) {
            this.nombre_completo = dataFromLocalStorage;
        }
        console.log(this.idtarifa);
        this.iduser = window.Laravel.user.id;
        if (this.isLoggedin) {
            window.location.href = "/login";
        }
        this.$axios.get("/sanctum/csrf-cookie").then((response) => {
            this.$axios
                .get(`/api/membership/${this.iduser}`)
                .then((response) => {
                    this.membership = response.data;
                    console.log(this.membership);
                })
                .catch(function (error) {
                    console.log(error);
                });
        });
        this.$axios.get("/sanctum/csrf-cookie").then((response) => {
            this.$axios
                .post("/api/tarifasSelect", {
                    id: this.idtarifa,
                })
                .then((response) => {
                    var tarifas = response.data;
                    this.tarfiasArray.push(tarifas);
                    console.log("XXXXX");
                    console.log(this.tarfiasArray);
                })
                .catch(function (error) {
                    console.error(error);
                });
        });
    },

    methods: {
        saveToLocalStorage() {
            if (this.nombre_completo.trim() !== "") {
                localStorage.setItem("dataPago", this.nombre_completo);
            }
        },
        cambiarTarifa(e) {
            e.preventDefault();
            this.$axios.get("/sanctum/csrf-cookie").then((response) => {
                this.$axios
                    .delete(
                        `/api/cambiarTarifas/${this.idtarifa}/${this.iduser}/${this.nombre_completo}/${this.numero_tarjeta}`
                    )
                    .then((response) => {
                        console.log(response.data);
                        window.location.href = `/dashboard/${this.iduser}`;
                        // const index = this.tarifas.findIndex(
                        //     (tarifa) => tarifa.id === id
                        // );
                        // this.tarifas.splice(index, 1);
                    })
                    .catch(function (error) {
                        console.log(error);
                    });
            });
            // this.$axios.get("/sanctum/csrf-cookie").then((response) => {
            //     this.$axios
            //         .delete(
            //             `/api/cambiarTarifas/${this.idtarifa}/${this.iduser}`
            //         )
            //         .then((response) => {
            //             const index = this.tarifas.findIndex(
            //                 (tarifa) => tarifa.id === id
            //             );
            //             this.tarifas.splice(index, 1);
            //         })
            //         .catch(function (error) {
            //             console.log(error);
            //         });
            // });
        },
    },
};
</script>
