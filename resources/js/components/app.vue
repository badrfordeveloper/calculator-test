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
        <h2>result : {{ formatedResult }}</h2>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      result: null,
      startWithMinus: false,
      textOperation: "",
      error: false,
    };
  },
  computed: {
    formatedResult() {
      return this.result != null && !isNaN(Number(this.result))  ? this.result.toLocaleString("en-US") : "";
    },
  },
  watch: {
    textOperation(newValue) {
      this.error = false;
      if (newValue == "") {
        this.reset();
      }
      // check regex that allow operation + integers + floats numbers
      else if (/^[-+]?[0-9]+(\.\d+(?!\.))?([-+/*](?![-+/*])|([-+/*][0-9]+(\.\d+(?!\.))?))*$/gs.test(newValue) == false) {
        this.error = true;
        this.result = null;
      } else {
        this.makeOperation();
      }
    },
  },
  methods: {
    reset() {
        this.result = null;
        this.startWithMinus = false;
        this.textOperation = "";
    },
    makeOperation() {
        let arrItems = [];
        let myNumber = "";
        // convert textOperation to an array of chars ['3','2','+','1','2']
        let arrChars = this.textOperation.split("");
        // convert arrChars to an array of items ['32','+','12']
        arrChars.forEach(async (element, index) => {
            if (index == arrChars.length - 1) {
            //if is number
            if (!isNaN(Number(element))) {
                myNumber += element;
                arrItems.push(myNumber);
                myNumber = "";
            } else if (["-", "*", "/", "+"].includes(element)) {
                arrItems.push(myNumber);
                myNumber = "";
            }
            } else if (["-", "*", "/", "+"].includes(element)) {
            arrItems.push(myNumber);
            myNumber = "";
            arrItems.push(element);
            } else {
            myNumber += element;
            }
        });
        //remove empty items
        arrItems = arrItems.filter((n) => n);
        myNumber = "";
        this.result = null;
        //start the calculation
        arrItems.forEach(async (element, index) => {
            //deal with first element if he is - or not
            if (index == 0) {
                if (element == "-") {
                    this.startWithMinus = true;
                } else if (["*", "/", "+"].includes(element)) {
                    this.startWithMinus = false;
                } else {
                    this.startWithMinus = false;
                    this.result = element;
                }
            }
            //first number
            else if (this.result == null) {
                this.result = this.startWithMinus ? -1 * element : element;
            }
            //catch an operation
            else if (["-", "*", "/", "+"].includes(element)) {
                myNumber = element;
            }
            //make the calculation
            //Number : convert string to an number
             else {
                this.result = Number(this.result);
                // check if element start with
                if (myNumber != "" && myNumber == "-") this.result -= Number(element);
                if (myNumber != "" && myNumber == "+") this.result += Number(element);
                if (myNumber != "" && myNumber == "*") this.result *= Number(element);
                if (myNumber != "" && myNumber == "/") this.result /= Number(element);
            }
            //+ remove ,00 and toFixed(2) keep only 2 digit behinf the coma
            this.result = await +Number(this.result).toFixed(2);
            //if operation go wrong
            if (isNaN(this.result)) {
                this.error = true;
                this.result = null;
            }
        });
    },
  },
};
</script>

<style >
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
