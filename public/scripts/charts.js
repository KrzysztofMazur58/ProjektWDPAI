let consumedCalories = 0;
let remainingCalories = 0;

let dailyCalories = 0;
let userId = 0;

let consumedProtein = 0;
let consumedFat = 0;
let consumedCarbs = 0;

let dailyProtein = 0;
let dailyFat = 0;
let dailyCarbs = 0;

let servingWeight = 0;

let charts = {};

function drawChart(chartId, consumed, daily, color1, color2) {
    const ctx = document.getElementById(chartId).getContext('2d');

    if (charts[chartId]) charts[chartId].destroy();

    charts[chartId] = new Chart(ctx, {
        type: 'doughnut',
        data: {
            datasets: [{
                label: chartId,
                data: [consumed, Math.max(0, daily - consumed)],
                backgroundColor: [color1, color2],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'top' },
                tooltip: { enabled: true }
            },
            animation: {
                duration: 1000,
                easing: 'easeInOutQuad'
            }
        }
    });
}

function updateCharts() {
    drawChart('calorieChart', consumedCalories, dailyCalories, '#3AA0FE', '#0F0872');
    drawChart('proteinChart', consumedProtein, dailyProtein, '#3AA0FE', '#0F0872');
    drawChart('fatChart', consumedFat, dailyFat, '#3AA0FE', '#0F0872');
    drawChart('carbsChart', consumedCarbs, dailyCarbs, '#3AA0FE', '#0F0872');
}

function fetchUserData() {
    fetch('get_user_data')  // Zmieniono na odpowiednią trasę dla metody getUserData
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                console.error('Error fetching data:', data.error);
                alert('Unable to load user data.');
                return;
            }

            dailyCalories = data.daily_calories || 0;
            consumedCalories = data.consumed_calories || 0;
            dailyProtein = data.daily_protein || 0;
            consumedProtein = data.consumed_protein || 0;
            dailyFat = data.daily_fat || 0;
            consumedFat = data.consumed_fat || 0;
            dailyCarbs = data.daily_carbs || 0;
            consumedCarbs = data.consumed_carbs || 0;

            document.getElementById("remainingCalories").innerText =
                `${Math.round(consumedCalories)} / ${Math.round(dailyCalories)}`;

            updateCharts();
            getMacronutrientAdvice();
            getNutritionTip();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('There was a problem fetching user data.');
        });
}

function fetchProductData(productQuery, mealWeight) {
    return fetch('get_meal_data', {  // Zmieniono na odpowiednią trasę dla metody getMealData
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ query: productQuery })
    })
        .then(response => response.json())
        .then(data => {
            if (data.calories !== undefined) {
                const apiServingWeight = data.serving_weight_grams || 0;
                const apiCalories = data.calories || 0;
                const apiProtein = data.protein || 0;
                const apiFat = data.fat || 0;
                const apiCarbs = data.carbohydrates || 0;

                if (apiServingWeight > 0) {
                    const scaleFactor = mealWeight / apiServingWeight;
                    return {
                        calories: Math.round(apiCalories * scaleFactor),
                        protein: Math.round(apiProtein * scaleFactor),
                        fat: Math.round(apiFat * scaleFactor),
                        carbs: Math.round(apiCarbs * scaleFactor),
                        servingWeight: mealWeight
                    };
                }
            }
            throw new Error('Product not found.');
        });
}

function addProduct(productQuery, mealWeight) {
    if (!productQuery || !mealWeight) {
        alert("Please provide both a product name and meal weight.");
        return;
    }

    fetchProductData(productQuery, mealWeight)
        .then(nutritionalInfo => {
            consumedCalories += nutritionalInfo.calories;
            consumedProtein += nutritionalInfo.protein;
            consumedFat += nutritionalInfo.fat;
            consumedCarbs += nutritionalInfo.carbs;

            servingWeight = nutritionalInfo.servingWeight;

            document.getElementById("remainingCalories").innerText =
                `${Math.round(consumedCalories)} / ${Math.round(dailyCalories)}`;

            saveConsumedCalories();
            updateCharts();
            getMacronutrientAdvice();

            const addedMealMessage = document.getElementById("addedMealMessage");
            addedMealMessage.innerText = `Added product: ${productQuery}, serving weight: ${servingWeight}g`;
            addedMealMessage.style.display = "block";
        })
        .catch(error => {
            console.error('Error:', error);
            alert('There was a problem adding the product.');
        });
}

function saveConsumedCalories() {
    fetch('update_user_data', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            consumedCalories,
            consumedProtein,
            consumedFat,
            consumedCarbs
        })
    })
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                alert('Error saving data.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('There was a problem saving data.');
        });
}

function getNutritionTip() {
    const hour = new Date().getHours();

    fetch(`get_nutrition_tip?hour=${hour}`)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                console.error('API Error:', data.error);
            } else {
                document.getElementById("nutritionTip").innerText = data.tip;
                document.getElementById("nutritionBenefit").innerText = data.benefit;
            }
        })
        .catch(error => {
            console.error('Error fetching nutrition tip:', error);
        });
}

function getMacronutrientAdvice() {
    let advice = "";
    const hour = new Date().getHours();

    const macronutrientConditions = {
        protein: [
            { condition: (value) => value > 100, advice: "💪 Your protein intake is over 100%. You may want to adjust your protein intake to avoid excess." },
            { condition: (value) => value < 30, advice: "💪 Your protein intake is below 30%. Consider adding more protein-rich foods like lean meats or legumes." },
            { condition: (value) => value > 60 && value <= 100, advice: "💪 Your protein intake is above 60%. Ensure you're not overdoing it!" }
        ],
        fat: [
            { condition: (value) => value > 100, advice: "🥑 Your fat intake is over 100%. Be cautious with your fat intake to prevent overconsumption." },
            { condition: (value) => value < 30, advice: "🥑 Your fat intake is below 30%. Try including healthy fats like avocados, nuts, and olive oil." },
            { condition: (value) => value > 60 && value <= 100, advice: "🥑 Your fat intake is above 60%. Make sure to balance your fat intake with healthy options." }
        ],
        carbs: [
            { condition: (value) => value > 100, advice: "🍞 Your carbohydrate intake is over 100%. Too many carbs can affect your overall balance." },
            { condition: (value) => value < 30, advice: "🍞 Your carbohydrate intake is below 30%. Consider adding more whole grains, fruits, and vegetables." }
        ]
    };

    if (dailyProtein && dailyFat && dailyCarbs && consumedProtein >= 0 && consumedFat >= 0 && consumedCarbs >= 0) {
        const proteinPercentage = (consumedProtein / dailyProtein) * 100;
        const fatPercentage = (consumedFat / dailyFat) * 100;
        const carbsPercentage = (consumedCarbs / dailyCarbs) * 100;
        let isOver = false;

        console.log('Protein:', proteinPercentage, 'Fat:', fatPercentage, 'Carbs:', carbsPercentage);

        ['protein', 'fat', 'carbs'].forEach(nutrient => {
            macronutrientConditions[nutrient].forEach(cond => {
                const nutrientPercentage = window[nutrient + 'Percentage'] || 0;
                if (cond.condition(nutrientPercentage)) {
                    advice += cond.advice + "\n";
                    if (cond.condition === macronutrientConditions[nutrient][0].condition) isOver = true;
                }
            });
        });

    } else if (hour >= 13 && (dailyProtein === 0 || dailyFat === 0 || dailyCarbs === 0)) {
        if (dailyProtein === 0) {
            advice += "💪 Your protein intake is below 30%. Consider adding more protein-rich foods like lean meats or legumes.";
        }
        if (dailyFat === 0) {
            advice += "\n🥑 Your fat intake is below 30%. Try including healthy fats like avocados, nuts, and olive oil.";
        }
        if (dailyCarbs === 0) {
            advice += "\n🍞 Your carbohydrate intake is below 30%. Consider adding more whole grains, fruits, and vegetables.";
        }
    } else {
        advice = "❗ Data is missing or invalid. Please try again.";
    }

    console.log('Macronutrient Advice:', advice);
    document.getElementById("macronutrientAdvice").innerText = advice;
}


document.getElementById("checkButton").addEventListener("click", function () {
    const productQuery = document.getElementById("productQuery").value;
    const mealWeight = document.getElementById("mealWeight").value;

    if (!productQuery || !mealWeight) {
        alert("Please provide both a product name and meal weight.");
        return;
    }

    const addedMealMessage = document.getElementById("addedMealMessage");
    addedMealMessage.style.display = "none";

    fetchProductData(productQuery, mealWeight)
        .then(nutritionalInfo => {
            const nutritionalInfoElement = document.getElementById("nutritionalInfo");
            nutritionalInfoElement.innerHTML = `
                <p>Calories: ${nutritionalInfo.calories} kcal</p>
                <p>Protein: ${nutritionalInfo.protein} g</p>
                <p>Fat: ${nutritionalInfo.fat} g</p>
                <p>Carbohydrates: ${nutritionalInfo.carbs} g</p>
            `;
            nutritionalInfoElement.style.display = "block";
        })
        .catch(error => {
            console.error('Error:', error);
            alert('There was a problem fetching the product data.');
        });
});

document.getElementById("confirmButton").addEventListener("click", function () {
    const productQuery = document.getElementById("productQuery").value;
    const mealWeight = document.getElementById("mealWeight").value;

    if (!productQuery || !mealWeight) {
        alert("Please provide both a product name and meal weight.");
        return;
    }

    const nutritionalInfoElement = document.getElementById("nutritionalInfo");
    nutritionalInfoElement.style.display = "none";

    addProduct(productQuery, mealWeight);
});

window.onload = function () {
    fetchUserData();
};





