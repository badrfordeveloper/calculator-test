<template>
  <div class="container">
    <div class="login-box">
      <h2>Calculator</h2>
      <span v-show="error" style="color: red">Please make sure your syntax is correct and without spaces</span>
      <div>
        <div class="user-box">
          <textarea
            pattern="[0-9]+"
            v-model="textOperation"
            placeholder="ex : 50-40.2+10"
          ></textarea>
        </div>
        <button class="btn" @click="calculate"> {{ loading ? 'laoding..' : 'calculate'}}</button>
        <h2 v-show="result">result : {{ formatedResult }}</h2>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'
export default {
  data() {
    return {
      result: null,
      loading: false,
      textOperation: "",
      error: false,
    };
  },
  computed: {
    formatedResult() {
      return this.result != null && !isNaN(Number(this.result))  ? this.result.toLocaleString("en-US") : "";
    },
  },
  methods: {
    reset() {
        this.result = null;
        this.textOperation = "";
    },
    calculate(){
      //remove spaces
      this.textOperation = this.textOperation.replace(/\s/g, '');
      this.error = false;
      if (this.textOperation == "") {
        this.reset();
      }
      // check regex that allow operations + integers + floats numbers
      else if (/^[-+]?[0-9]+(\.\d+(?!\.))?([-+/*](?![-+/*])|([-+/*][0-9]+(\.\d+(?!\.))?))*$/gs.test(this.textOperation) == false) {
        this.error = true;
        this.result = null;
      } else {
        this.makeOperation();
      }
    },
    async  makeOperation() {
        this.loading=true;
        await axios.post('/calculate', {
            textOperation: this.textOperation,
        })
        .then( (response) => {
            this.error = false;
            this.result = response.data;
            this.loading=false;
        })
        .catch( (erreur) => {
                this.loading=false;
                this.error = true;
                this.result = null;
        });
    },
  },
};
</script>

<style >
.btn{
    width: 141px;
    margin: auto;
    color: white;
    display: block;
    background-color: #002048;
    padding: 11px;
    margin-bottom: 13px;
    font-size: 16px;
}
html {
  height: 100%;
}
body {
  margin: 0;
  padding: 0;
  font-family: sans-serif;
  background: linear-gradient(#141e30, #243b55);
}

.login-box {
  position: absolute;
  top: 50%;
  left: 50%;
  width: 510px;
  padding: 40px;
  transform: translate(-50%, -50%);
  background: rgba(0, 0, 0, 0.5);
  box-sizing: border-box;
  box-shadow: 0 15px 25px rgba(0, 0, 0, 0.6);
  border-radius: 10px;
}

.login-box h2 {
  margin: 0 0 30px;
  padding: 0;
  color: #fff;
  text-align: center;
}

.login-box .user-box {
  position: relative;
}

.login-box .user-box textarea {
  width: 100%;
  padding: 10px 0;
  font-size: 19px;
  color: #fff;
  margin-bottom: 30px;
  border: none;
  border-bottom: 1px solid #fff;
  outline: none;
  background: transparent;
}
</style>
