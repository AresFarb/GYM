<template>
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between pb-2 mb-2">
                <h5 class="card-title">Update User</h5>
            </div>

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

            <form @submit.prevent="updatePost" enctype="multipart/form-data">
                <div class="form-group mb-2">
                    <label>Name</label><span class="text-danger"> *</span>
                    <input
                        type="text"
                        class="form-control"
                        v-model="name"
                        placeholder="Enter post name"
                    />
                </div>
                <div class="form-group mb-2">
                    <label>email</label><span class="text-danger"> *</span>
                    <input
                        type="text"
                        class="form-control"
                        v-model="email"
                        placeholder="Enter post name"
                    />
                </div>
                <div class="form-group mb-2">
                    <label>Contraseña</label><span class="text-danger"> *</span>
                    <input
                        type="password"
                        class="form-control"
                        v-model="password"
                        placeholder="Enter post name"
                    />
                </div>
                <div class="form-group mb-2">
                    <label>Fecha de Nacimiento</label
                    ><span class="text-danger"> *</span>
                    <input
                        type="date"
                        class="form-control"
                        v-model="fecha_nacimiento"
                        placeholder="Enter post name"
                    />
                </div>
                <div class="form-group mb-2">
                    <label>Roles Actuales</label
                    ><span class="text-danger"> *</span>
                    <tbody>
                        <tr v-for="(role, index) in roles" :key="role.id">
                            <td>{{ role.id }}</td>
                            <td>{{ role.nombre_role }}</td>

                            <div>
                                <input 
                                    type="checkbox"
                                    name="test"
                                    :value="`${role.id}`"
                                    v-model="checked"
                                />
                            </div>
                           
                            
                        </tr>
                    </tbody>
                    {{ checked }}
                </div>
                <button
                    type="submit"
                    class="btn button-primary mt-4 mb-4"
                    @click="updateUser"
                >
                    Update User
                </button>

                <!-- {{ this.user_role }} -->
            </form>
        </div>
    </div>
</template>

<script>
export default {
    name: "updateUsers",
    data() {
        return {
            id: "",
            name: "",
            email: "",
            password: "",
            fecha_nacimiento: "",
            user_role: null,
            iduser: this.$route.params.id,
            roles: [],
            checked: [],
            strSuccess: "",
            strError: "",
        };
    },
    mounted() {
        this.user_role = window.Laravel.user_role;
        console.log(this.user_role[0]);
        console.log("dsadas");
        // console.log(window.Laravel.user.roles[0].nombre_role);
        this.$axios.get("/sanctum/csrf-cookie").then((response) => {
            this.$axios
                .get(`/api/showUserUpdate/${this.iduser}`)
                .then((response) => {
                    console.log(response.data.name);
                    this.name = response.data.name;
                    this.email = response.data.email;
                    this.password = response.data.password;
                    this.fecha_nacimiento = response.data.fecha_nacimiento;
                })
                .catch(function (error) {
                    console.log(error);
                });
        });
        this.$axios.get("/sanctum/csrf-cookie").then((response) => {
            this.$axios
                .get("/api/roles")
                .then((response) => {
                    console.log(response);
                    this.roles = response.data;
                    console.log(this.roles);
                    this.existeRole();
                })
                .catch(function (error) {
                    console.log(error);
                });
        });
        //belongsToMany para coger la relacion de los roles con usuarios
        this.$axios.get("/sanctum/csrf-cookie").then((response) => {
            this.$axios
                .get(`/api/rolesUser/${this.$route.params.id}`)
                .then((response) => {
                    this.user_role = window.Laravel.user_role;
                    console.log(response.data);
                })
                .catch(function (error) {
                    console.log(error);
                });
        });
    },
    methods: {
        existeRole() {
            let list_name_role =  this.user_role.map(role => role.nombre_role);
            this.roles.forEach((role,index) => {          
                (list_name_role.includes(role.nombre_role) )? this.checked[index].push(role.id): null;
            });
        },
        updateUser(e) {
            this.$axios.get("/sanctum/csrf-cookie").then((response) => {
                let existingObj = this;
                const config = {
                    headers: {
                        "content-type": "multipart/form-data",
                    },
                };

                const formData = new FormData();
                formData.append("name", this.name);
                formData.append("email", this.email);
                formData.append("password", this.password);
                formData.append("fecha_nacimiento", this.fecha_nacimiento);
                formData.append("role_id",this.checked);

                this.$axios
                    .post(
                        `/api/update/${this.$route.params.id}/${this.checked}`,
                        formData,
                        config
                    )
                    .then((response) => {
                        existingObj.strError = "";
                        existingObj.strSuccess = response.data.success;
                    })
                    .catch(function (error) {
                        existingObj.strSuccess = "";
                        existingObj.strError = error.response.data.message;
                    });
            });
        },

        beforeRouteEnter(to, from, next) {
            if (!window.Laravel.isLoggedin) {
                window.location.href = "/";
            }
            next();
        },
    },
};
</script>
